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
use app\models\ControlProcesosEquipos;
use app\models\ControlProcesos;
use app\models\ControlParams;
use app\models\ControlSeguimientoProcesos;
use app\models\Seguimientorendimiento;


class SeguimientoprocesosController extends \yii\web\Controller {

		public function behaviors(){
			return[
				'access' => [
						'class' => AccessControl::classname(),
						'only' => ['view', 'justificar', 'searchdate', 'searchpcrc', 'viewpcrc', 'graficos1', 'graficos2', 'graficos3', 'graficos4', 'comparar', 'dimensiones', 'formglobal', 'formglobal2','detallemesactual','detallemescorte','pcrcpadre'],
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

		public function actions() {
            return [
                'error' => [
                  'class' => 'yii\web\ErrorAction',
                ]
            ];
        }
    
        public function actionError() {
    
            //ERROR PRESENTADO
            $exception = Yii::$app->errorHandler->exception;
    
            if ($exception !== null) {
                //VARIABLES PARA LA VISTA ERROR
                $code = $exception->statusCode;
                $name = $exception->getName() . " (#$code)";
                $message = $exception->getMessage();
                //VALIDO QUE EL ERROR VENGA DEL CLIENTE DE IVR Y QUE SOLO APLIQUE
                // PARA LOS ERRORES 400
                $request = \Yii::$app->request->pathInfo;
                if ($request == "basesatisfaccion/clientebasesatisfaccion" && $code ==
                        400) {
                    //GUARDO EN EL ERROR DE SATU
                    $baseSat = new BasesatisfaccionController();
                    $baseSat->setErrorSatu(\Yii::$app->request->url, $name . ": " . $message);
                }
                //RENDERIZO LA VISTA
                return $this->render('error', [
                            'name' => $name,
                            'message' => $message,
                            'exception' => $exception,
                ]);
            }
        }
		
		/**
		*Accion que permite mostrar en pantalla el listados de los datos.
		*@return mixed
		*/
		public function actionIndex(){		
			$model = new ControlSeguimientoProcesos();
			$sessiones = Yii::$app->user->identity->id;
			$dataProvider = $model->searchseguimiento(Yii::$app->request->post());
			$txtvalorador = $model->evaluados_id;

			return $this->render('index',[
				'model'=>$model,
				'dataProvider'=>$dataProvider,
				'sessiones'=>$sessiones,
				'txtvalorador' => $txtvalorador,
				]);
		}

		/**
		*Accion que permite mostrar en pantalla el listados de los datos.
		*@return mixed
		*/
		public function actionSearchpcrc(){	
			$model = new ControlParams();			
			$sessiones = Yii::$app->user->identity->id;
			$dataProvider = $model->Obtenerseguimiento(Yii::$app->request->post());
			$txtvalorador = $model->evaluados_id;

			return $this->render('_formpcrc',[
				'model'=>$model,
				'dataProvider'=>$dataProvider,
				'sessiones'=>$sessiones,
				'txtvalorador' => $txtvalorador,
				]);
		}

		/**
		*Accion que permite mostrar en pantalla el listados de los datos.
		*@return mixed
		*/
		public function actionSearchdate(){		
			$model = new ControlSeguimientoProcesos();
			$sessiones = Yii::$app->user->identity->id;
			$dataProvider = $model->searchseguimientofecha(Yii::$app->request->post());
			$txtvalorador = $model->evaluados_id;

			return $this->render('_formdate',[
				'model'=>$model,
				'dataProvider'=>$dataProvider,
				'sessiones'=>$sessiones,
				'txtvalorador' => $txtvalorador,
				]);
		}

		/**
		*Accion que permite ver un usuario y sus dimensiones.
		*@return mixed
		*/
		public function actionView($id, $evaluados_id){
			$model = new \app\models\ControlProcesos();
			$model2 = new Controlparams();
			$dataProvider2 = null;
			$idValorador = null;
			$nomValorador = null;

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


			$idValorador = $evaluados_id;
			$varCero = 0;
			$nomValorador = Yii::$app->db->createCommand('select usua_nombre from tbl_usuarios where usua_id ='.$idValorador.'')->queryScalar();

			$varNametc = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0 and tipo_corte like "%'.$txtMes.'%" and evaluados_id ='.$idValorador.' and anulado ='.$varCero.'')->queryScalar();

			$fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$varNametc' and anulado = 0")->queryScalar();
        	$fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$varNametc' and anulado = 0")->queryScalar();  

			$dataProvider2 = $model2->Obtener2($id, $evaluados_id);

			return $this->render('view', [
				'model' => $model,
				'nomValorador' => $nomValorador,
				'idValorador' => $idValorador,
				'dataProvider2' => $dataProvider2,				
				'varNametc' => $varNametc,
				'fechainiC' => $fechainiC,
				'fechafinC' => $fechafinC,
				]);
		}	

		/**
		*Accion que permite ver un usuario y sus dimensiones.
		*@return mixed
		*/
		public function actionJustificar($nomVar){
			$varName = $nomVar;
			$varIdUsua = $varName;

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


			$idValorador = $varName;
			$nomValorador = Yii::$app->db->createCommand('select usua_nombre from tbl_usuarios where usua_id ='.$idValorador.'')->queryScalar();

			$varName = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0 and tipo_corte like "%'.$txtMes.'%" and evaluados_id ='.$idValorador.'')->queryScalar();


			$model = new Seguimientorendimiento();
			$sessiones = Yii::$app->user->identity->id;

			$nameVar = Yii::$app->db->createCommand('select usua_nombre from tbl_usuarios where usua_id ='.$idValorador.'')->queryScalar();

			$varidtc = Yii::$app->db->createCommand("select idtc from tbl_tipocortes where tipocortetc like '$varName' and anulado = 0")->queryScalar();

			$formData = Yii::$app->request->post();

			if ($model->load($formData)) {
				if ($model->save()) {
						Yii::$app->getsession()->setFlash('message','Registro agregado correctamente');

						$varDestino = $model->correo;

						$nomtipoCorte1 = $model->idtiposcortes;
						$nomtipoCorte2 = Yii::$app->db->createCommand('select diastcs from tbl_tipos_cortes where idtcs = '.$nomtipoCorte1.'')->queryScalar();

						$phpExc = new \PHPExcel();

						$phpExc->getProperties()
                        	->setCreator("Konecta")
                        	->setLastModifiedBy("Konecta")
                        	->setTitle("Justificacion de Rendimiento - Valoraciones QA")
                        	->setSubject("Justificacion de Rendimiento - Valoraciones QA")
                        	->setDescription("Este archivo genera la justificacion del rednimiento de las valoraciones de un tecnico.")
                        	->setKeywords("Justificacion de Rendimiento - Valoraciones QA");
                		$phpExc->setActiveSheetIndex(0);

                		$numCell = 1;
                		$phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'VALORADOR');
                		$numCell = $numCell++ + 1;
                		$phpExc->getActiveSheet()->setCellValue('A'.$numCell, $nameVar);
                		$numCell = $numCell + 2; 
                		$phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'TIPOS DE CORTES');
                		$phpExc->getActiveSheet()->setCellValue('B'.$numCell, 'JUSTIFICACION');
                		$phpExc->getActiveSheet()->setCellValue('C'.$numCell, 'CORREO ELECTRONICO');
                		$numCell = $numCell++ + 1;
                		$phpExc->getActiveSheet()->setCellValue('A'.$numCell, $nomtipoCorte2);
                		$phpExc->getActiveSheet()->setCellValue('B'.$numCell, $model->justificacion);
                		$phpExc->getActiveSheet()->setCellValue('C'.$numCell, $model->correo);

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
		                        ->setSubject("Envio de Justificacion de Rendimiento QA")
		                        ->attach($tmpFile)
		                        ->setHtmlBody($message)
		                        ->send();

	                    return $this->redirect(['index']);
				}
			}

			return $this->render('justificacion', [
					'model' => $model,
					'varidtc' => $varidtc,
					'varName' => $varName,
					'nameVar' => $nameVar,
					'sessiones' => $sessiones,
					'varIdUsua' => $varIdUsua,
				]);
		}

		/**
		*Accion que permite ver un usuario y sus dimensiones.
		*@return mixed
		*/
		public function actionViewpcrc($arbolid){
			$model = new ControlParams();
			$varArbol = $arbolid;
			$varNomArbol = Yii::$app->db->createCommand('select name from tbl_arbols where id = '.$varArbol.'')->queryScalar();

			$dataProvider = $model->Obtenerpcrc($varArbol);

			return $this->render('viewpcrc', [
					'varNomArbol' => $varNomArbol,
					'dataProvider' => $dataProvider,
				]);
		}

		/**
		*Accion que permite seleccionar el tipo de grafica que se desee.
		*@return mixed
		*/
		public function actionGraficos1(){
			return $this->renderAjax('_formgrafic1');
		}

		/**
		*Accion que permite seleccionar el tipo de grafica que se desee.
		*@return mixed
		*/
		public function actionGraficos2($evaluados_id){
			$idval = $evaluados_id;
			$nameVal = Yii::$app->db->createCommand('select usua_nombre from tbl_usuarios where usua_id ='.$idval.'')->queryScalar();	
			$model = new ControlSeguimientoProcesos();
			
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
			$txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0 and evaluados_id ='.$idval.' and tipo_corte like "%'.$txtMes.'%" and anulado ='.$varCero.'')->queryScalar(); 
        	$fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();
        	$fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar(); 

			
			$querys =  new Query;
        	$querys ->select(['tbl_arbols.name', 'count(*) as Total'])
                    ->from('tbl_control_params')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_control_params.arbol_id = tbl_arbols.id')
                    ->where('tbl_control_params.evaluados_id = '.$idval.'')
	            ->andwhere(['tbl_control_params.anulado' => 'null'])
                    ->andwhere(['between','tbl_control_params.fechacreacion',$fechainiC,$fechafinC])
                    ->groupBy('tbl_control_params.arbol_id');
            $command = $querys->createCommand();
            $data = $command->queryAll();    


			return $this->render('_formgrafic2', [
					'model' => $model,
					'nameVal' => $nameVal,
					'data' => $data,
					'idval' => $idval,
				]);
		}


		/**
		*Accion que permite seleccionar el tipo de grafica que se desee.
		*@return mixed
		*/
		public function actionGraficos3($evaluados_id){
			$idval = $evaluados_id;
			$varCero = 0;
			$nameVal = Yii::$app->db->createCommand('select usua_nombre from tbl_usuarios where usua_id ='.$idval.'')->queryScalar();	
			$model = new ControlSeguimientoProcesos();

			 $querys = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where evaluados_id ='.$idval.' and anulado ='.$varCero.'')->queryScalar();

         	$dataFI = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$querys' and anulado = 0")->queryScalar();

         	$dataFF = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$querys' and anulado = 0")->queryScalar();
		

			
			$querys =  new Query;
        	$querys ->select(['date_format(tbl_ejecucionformularios.created, "%Y/%m/%d")  as fecha', 'count(tbl_ejecucionformularios.created) as Total'])
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
                    ->where(['between','tbl_ejecucionformularios.created', $dataFI, $dataFF])
                    ->andwhere('tbl_usuarios.usua_id = '.$idval.'')
                    ->groupBy('fecha');
            $command = $querys->createCommand();
            $data = $command->queryAll(); 

			return $this->render('_formgrafic3', [
					'model' => $model,
					'nameVal' => $nameVal,
					'data' => $data,
					'idval' => $idval,
				]);
		}	

		/**
		*Accion que permite seleccionar el tipo de grafica que se desee.
		*@return mixed
		*/
		public function actionGraficos4($arbolid){
			$idval = $arbolid;
			$month = date('m');
        	$year = date('Y');
        	$day = date("d", mktime(0,0,0, $month+1, 0, $year));
        	$fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        	$fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year)); 

			$nomPcrc = Yii::$app->db->createCommand('select name from tbl_arbols where id ='.$arbolid.'')->queryScalar();

			$querys =  new Query;
        	$querys ->select(['tbl_control_params.fechacreacion as fecha', 'count(tbl_control_params.arbol_id) as Total'])
                    ->from('tbl_control_params')
                    ->where(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC])
                    ->andwhere('tbl_control_params.arbol_id = '.$idval.'')
                    ->groupBy('fecha');
            $command = $querys->createCommand();
            $data = $command->queryAll(); 

			return $this->render('_formgrafic4', [
					'nomPcrc' => $nomPcrc,
					'data' => $data,
				]);
		}	


		/**
		*Accion que permite seleccionar el tipo de grafica que se desee.
		*@return mixed
		*/
		public function actionGraficos5($dimensions){
			$idval = $dimensions;
			$month = date('m');
        	$year = date('Y');
        	$day = date("d", mktime(0,0,0, $month+1, 0, $year));

        	$fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        	$fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year)); 

			$nomDimens = Yii::$app->db->createCommand('select dimensions from tbl_control_params where dimensions like "%'.$idval.'%"')->queryScalar();

			$querys =  new Query;
        	$querys ->select(['count(tbl_control_params.dimensions) as Total', 'tbl_control_params.fechacreacion as fecha'])
                    ->from('tbl_control_params')
                    ->where(['anulado' => 'null'])
		    ->where(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC])
		    ->andwhere('tbl_control_params.dimensions like "%'.$idval.'%"')  
                    ->andwhere("tbl_control_params.anulado = 0")                 
                    ->groupBy('fechacreacion');
            $command = $querys->createCommand();
            $data = $command->queryAll(); 

			return $this->render('_formgrafic5', [
					'nomDimens' => $nomDimens,
					'data' => $data,
				]);
		}			


		/**
		*Accion para comparar los valoradores asociados al coordinador
		*@return mixed
		*/
		public function actionComparar(){
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

			$querys =  new Query;
        	$querys ->select(['tbl_control_params.evaluados_id', 'tbl_usuarios.usua_nombre', 'tbl_arbols.name', 'tbl_control_params.dimensions', 'tbl_control_params.cant_valor', 'tbl_control_procesos.tipo_corte'])
                    ->from('tbl_control_params')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'tbl_control_params.evaluados_id = tbl_usuarios.usua_id')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_control_params.arbol_id = tbl_arbols.id')
                    ->join('LEFT OUTER JOIN', 'tbl_control_procesos',
                            'tbl_control_params.evaluados_id = tbl_control_procesos.evaluados_id')     
                    ->where(['tbl_control_params.anulado' => '0'])
		    ->andwhere("tbl_control_procesos.anulado = 0")
                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC])
					->andwhere('tbl_control_procesos.responsable = '.$sessiones.'');
            $command = $querys->createCommand();
            $data = $command->queryAll(); 

			return $this->render('_formcomparar',[
					'sessiones' => $sessiones,
					'data' => $data,
				]);
		}


		/**
		*Accion para buscar por dimensiones
		*@return mixed
		*/
		public function actionDimensiones(){
			$model = new ControlParams();
			$dataProvider = $model->Obtenerdimensiones(Yii::$app->request->post());			

			return $this->render('_formdimensiones', [
					'model' => $model,
					'dataProvider' => $dataProvider,
				]);
		}		

		
		/**
		*Accion que permite ver las dimensiones.
		*@return mixed
		*/
		public function actionViewdimensions($dimensions){
			$model = new ControlParams();
			$varDimens = $dimensions;
			$varNomDimens = Yii::$app->db->createCommand('select dimensions from tbl_control_params where dimensions like "%'.$varDimens.'%"')->queryScalar();

			$dataProvider = $model->Obtenerdimensions($varDimens);

			return $this->render('viewdimensions', [
					'varNomDimens' => $varNomDimens,
					'dataProvider' => $dataProvider,
				]);
		}


/**
		*Accion que permite dirigir y ver la grafica del equipo global.
		*@return mixed
		*/
		public function actionFormglobal(){
			$model = new \app\models\ControlProcesos();
			$sessiones = Yii::$app->user->identity->id;
			$nameSessions = Yii::$app->user->identity->fullName;

			$month = date('m');
        	$year = date('Y');
        	$day = date("d", mktime(0,0,0, $month+1, 0, $year));			


        	$fechainiC = date('Y-m-d 00:00:00', mktime(0,0,0, $month, 1, $year));
        	$fechafinC = date('Y-m-d 23:59:59', mktime(0,0,0, $month, $day, $year)); 
			
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

	        $rol =  new Query;
	        $rol     ->select(['tbl_roles.role_id'])
	                    ->from('tbl_roles')
	                    ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
	                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
	                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
	                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
	                    ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
	        $command = $rol->createCommand();
	        $roles = $command->queryScalar();

	        if ($roles == "270") {
	        	$varEvaluados1 = Yii::$app->db->createCommand('select evaluados_id from tbl_control_procesos where anulado = 0 and  tipo_corte like "%'.$txtMes.'%"')->queryAll();

	        	$varEvaluados = array();
				for ($i=0; $i < count($varEvaluados1) ; $i++) { 
					array_push($varEvaluados, $varEvaluados1[$i]["evaluados_id"]);
				}

				$listados = (array)($varEvaluados);
				$ids = "'" . implode("','", $listados) . "'";

				$querys =  new Query;
		        	$querys ->select(['date_format(tbl_ejecucionformularios.created, "%Y/%m/%d")  as fecha', 'count(tbl_ejecucionformularios.created) as total'])
		                    ->from('tbl_ejecucionformularios')
		                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
		                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
		                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
		                    //->andwhere('tbl_usuarios.usua_id = '.$txtIdValorador.'')
		                    ->andwhere( " tbl_usuarios.usua_id in ($ids)")
		                    ->groupBy('fecha');
		            	$command = $querys->createCommand();
		            	$data = $command->queryAll(); 	 
	        }
	        else
	        {
	        	if ($roles == "276") {
	        		if ($sessiones == "70") {
		        		$querys =  new Query;                
	                	$querys     ->select(['tbl_control_procesos.evaluados_id'])->distinct()
	                                	->from('tbl_control_procesos')
		                                ->join('LEFT OUTER JOIN', 'tbl_control_params',
	        	                                'tbl_control_procesos.evaluados_id = tbl_control_params.evaluados_id')
										->join('LEFT OUTER JOIN', 'tbl_arbols',
					        	                                'tbl_control_params.arbol_id = tbl_arbols.id')
										->join('LEFT OUTER JOIN', 'tbl_usuarios',
					        	                                'tbl_control_params.evaluados_id = tbl_usuarios.usua_id')
										->where(['tbl_control_procesos.anulado' => 'null'])
										->andwhere(['tbl_arbols.arbol_id' => 17])
										->andwhere(['like','tbl_control_procesos.tipo_corte', $txtMes])
	                	                ->andwhere(['between','tbl_control_procesos.fechacreacion', $fechainiC, $fechafinC]);                             
		                $command = $querys->createCommand();
	        	        $varEvaluados1 = $command->queryAll(); 

	        	        $varEvaluados = array();
						for ($i=0; $i < count($varEvaluados1) ; $i++) { 
							array_push($varEvaluados, $varEvaluados1[$i]["evaluados_id"]);
						}

						$listados = (array)($varEvaluados);
						$ids = "'" . implode("','", $listados) . "'";

						$querys =  new Query;
				        	$querys ->select(['date_format(tbl_ejecucionformularios.created, "%Y/%m/%d")  as fecha', 'count(tbl_ejecucionformularios.created) as total'])
				                    ->from('tbl_ejecucionformularios')
				                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
				                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
				                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
				                    //->andwhere('tbl_usuarios.usua_id = '.$txtIdValorador.'')
				                    ->andwhere( " tbl_usuarios.usua_id in ($ids)")
				                    ->groupBy('fecha');
				            	$command = $querys->createCommand();
				            	$data = $command->queryAll(); 	
	        		}
	        		else
	        		{
						$varEvaluados1 = Yii::$app->db->createCommand('select evaluados_id from tbl_control_procesos where anulado = 0 and  responsable ='.$sessiones.'')->queryAll();

						$varEvaluados = array();
						for ($i=0; $i < count($varEvaluados1) ; $i++) { 
							array_push($varEvaluados, $varEvaluados1[$i]["evaluados_id"]);
						}

						$listados = (array)($varEvaluados);
						$ids = "'" . implode("','", $listados) . "'";

						$querys =  new Query;
				        	$querys ->select(['date_format(tbl_ejecucionformularios.created, "%Y/%m/%d")  as fecha', 'count(tbl_ejecucionformularios.created) as total'])
				                    ->from('tbl_ejecucionformularios')
				                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
				                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
				                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
				                    //->andwhere('tbl_usuarios.usua_id = '.$txtIdValorador.'')
				                    ->andwhere( " tbl_usuarios.usua_id in ($ids)")
				                    ->groupBy('fecha');
				            	$command = $querys->createCommand();
				            	$data = $command->queryAll(); 	
	        		}
	        	}
		        	
	        } 

			return $this->render('_formglobal', [
					'model' => $model,
					'data' => $data,
					'nameSessions' => $nameSessions,
				]);
		}


		/**
		*Accion que permite dirigir y ver la grafica del equipo global.
		*@return mixed
		*/
		public function actionFormglobal2(){
			$model = new \app\models\ControlProcesos();
			$sessiones = Yii::$app->user->identity->id;
			$nameSessions = Yii::$app->user->identity->fullName;
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


	        $rol =  new Query;
	        $rol     ->select(['tbl_roles.role_id'])
	                    ->from('tbl_roles')
	                    ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
	                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
	                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
	                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
	                    ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
	        $command = $rol->createCommand();
	        $roles = $command->queryScalar();

	        if($roles == "270")
	        {
	        	$txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0 and tipo_corte like "%'.$txtMes.'%"')->queryScalar(); 
			$fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();
			$fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar(); 

	        	$varEvaluados1 = Yii::$app->db->createCommand('select evaluados_id from tbl_control_procesos where anulado = 0 and  tipo_corte like "%'.$txtMes.'%"')->queryAll();

	        	$varEvaluados = array();
				for ($i=0; $i < count($varEvaluados1) ; $i++) { 
					array_push($varEvaluados, $varEvaluados1[$i]["evaluados_id"]);
				}

				$listados = (array)($varEvaluados);
				$ids = "'" . implode("','", $listados) . "'";

				$querys =  new Query;
		        	$querys ->select(['date_format(tbl_ejecucionformularios.created, "%Y/%m/%d")  as fecha', 'count(tbl_ejecucionformularios.created) as total'])
		                    ->from('tbl_ejecucionformularios')
		                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
		                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
		                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
		                    //->andwhere('tbl_usuarios.usua_id = '.$txtIdValorador.'')
		                    ->andwhere( " tbl_usuarios.usua_id in ($ids)")
		                    ->groupBy('fecha');
		            	$command = $querys->createCommand();
		            	$data = $command->queryAll(); 	 
	        }
	        else
	        {
	        	if ($roles == "276") {
	        		if ($sessiones == "70") {
	        			$txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0  and tipo_corte like "%'.$txtMes.'%" and tipo_corte like "%Bancolombia%"')->queryScalar(); 
			        	$fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();
			        	$fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar(); 

		        		$querys =  new Query;                
	                		$querys     ->select(['tbl_control_procesos.evaluados_id'])->distinct()
	                                	->from('tbl_control_procesos')
		                                ->join('LEFT OUTER JOIN', 'tbl_control_params',
	        	                                'tbl_control_procesos.evaluados_id = tbl_control_params.evaluados_id')
										->join('LEFT OUTER JOIN', 'tbl_arbols',
					        	                                'tbl_control_params.arbol_id = tbl_arbols.id')
										->join('LEFT OUTER JOIN', 'tbl_usuarios',
					        	                                'tbl_control_params.evaluados_id = tbl_usuarios.usua_id')
										->where(['tbl_control_procesos.anulado' => 'null'])
										->andwhere(['tbl_arbols.arbol_id' => 17])
										->andwhere(['like','tbl_control_procesos.tipo_corte', $txtMes])
	                	                ->andwhere(['between','tbl_control_procesos.fechacreacion', $fechainiC, $fechafinC]);                             
		                $command = $querys->createCommand();
	        	        $varEvaluados1 = $command->queryAll(); 

	        	        $varEvaluados = array();
						for ($i=0; $i < count($varEvaluados1) ; $i++) { 
							array_push($varEvaluados, $varEvaluados1[$i]["evaluados_id"]);
						}

						$listados = (array)($varEvaluados);
						$ids = "'" . implode("','", $listados) . "'";

						$querys =  new Query;
				        	$querys ->select(['date_format(tbl_ejecucionformularios.created, "%Y/%m/%d")  as fecha', 'count(tbl_ejecucionformularios.created) as total'])
				                    ->from('tbl_ejecucionformularios')
				                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
				                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
				                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
				                    //->andwhere('tbl_usuarios.usua_id = '.$txtIdValorador.'')
				                    ->andwhere( " tbl_usuarios.usua_id in ($ids)")
				                    ->groupBy('fecha');
				            	$command = $querys->createCommand();
				            	$data = $command->queryAll(); 	
	        		}
	        		else
	        		{
	        			$txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0 and responsable ='.$sessiones.' and tipo_corte like "%'.$txtMes.'%"')->queryScalar(); 
			        	$fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();
			        	$fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar(); 

						$varEvaluados1 = Yii::$app->db->createCommand('select evaluados_id from tbl_control_procesos where anulado = 0 and  responsable ='.$sessiones.'')->queryAll();

						$varEvaluados = array();
						for ($i=0; $i < count($varEvaluados1) ; $i++) { 
							array_push($varEvaluados, $varEvaluados1[$i]["evaluados_id"]);
						}

						$listados = (array)($varEvaluados);
						$ids = "'" . implode("','", $listados) . "'";

						$querys =  new Query;
				        	$querys ->select(['date_format(tbl_ejecucionformularios.created, "%Y/%m/%d")  as fecha', 'count(tbl_ejecucionformularios.created) as total'])
				                    ->from('tbl_ejecucionformularios')
				                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
				                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
				                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
				                    //->andwhere('tbl_usuarios.usua_id = '.$txtIdValorador.'')
				                    ->andwhere( " tbl_usuarios.usua_id in ($ids)")
				                    ->groupBy('fecha');
				            	$command = $querys->createCommand();
				            	$data = $command->queryAll(); 	
	        		}	        	
	        	}
	        }

			return $this->render('_formglobal2', [
					'model' => $model,
					'data' => $data,
					//'varTotal' => $varTotal,
					'txtcorte' => $txtcorte,
					'nameSessions' => $nameSessions,
				]);
		}


		/**
		*Accion que permite verificar el detalle del mes actual de las realizadas
		*@return mixed
		*/
		public function actionDetallemesactual(){
			$model = new \app\models\ControlProcesos();
			$sessiones = Yii::$app->user->identity->id;
			$nameSessions = Yii::$app->user->identity->fullName;

			$querys =  new Query;
        	$querys ->select(['tbl_control_params.evaluados_id', 'tbl_usuarios.usua_nombre'])->distinct()
                    ->from('tbl_control_params')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'tbl_control_params.evaluados_id = tbl_usuarios.usua_id')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_control_params.arbol_id = tbl_arbols.id')
                    ->join('LEFT OUTER JOIN', 'tbl_control_procesos',
                            'tbl_control_params.evaluados_id = tbl_control_procesos.evaluados_id')     
                    ->where(['tbl_control_params.anulado' => 'null'])
					->andwhere('tbl_control_procesos.responsable = '.$sessiones.'');
            $command = $querys->createCommand();
            $data = $command->queryAll(); 


			return $this->render('_formdetallemesactual', [
					'model' => $model,
					'data' => $data,
					//'varTotal' => $varTotal,
					'nameSessions' => $nameSessions,
				]);

		}	


		/**
		*Accion que permite verificar el detalle del mes del Corte de las realizadas
		*@return mixed
		*/
		public function actionDetallemescorte(){
			$model = new \app\models\ControlProcesos();
			$sessiones = Yii::$app->user->identity->id;
			$nameSessions = Yii::$app->user->identity->fullName;

			$querys =  new Query;
        	$querys ->select(['tbl_control_params.evaluados_id', 'tbl_usuarios.usua_nombre'])->distinct()
                    ->from('tbl_control_params')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'tbl_control_params.evaluados_id = tbl_usuarios.usua_id')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_control_params.arbol_id = tbl_arbols.id')
                    ->join('LEFT OUTER JOIN', 'tbl_control_procesos',
                            'tbl_control_params.evaluados_id = tbl_control_procesos.evaluados_id')     
                    ->where(['tbl_control_params.anulado' => 'null'])
					->andwhere('tbl_control_procesos.responsable = '.$sessiones.'');
            $command = $querys->createCommand();
            $data = $command->queryAll(); 


			return $this->render('_formdetallemescorte', [
					'model' => $model,
					'data' => $data,
					//'varTotal' => $varTotal,
					'nameSessions' => $nameSessions,
				]);

		}

		/**
		*Accion que permite mostrar en pantalla el listados de los datos.
		*@return mixed
		*/
		public function actionPcrcpadre(){	
			$model = new ControlParams();			
			$sessiones = Yii::$app->user->identity->id;
			$varAcciones = Yii::$app->request->post();
			$varArbolPadre = null;
			
			if (count(Yii::$app->request->post()) > 0) {
				$varAcciones1 = $varAcciones["ControlParams"]["arbol_id"];
				$varArbolPadre = $varAcciones1;						
				$dataProvider = $model->Obtenerpcrcpadre($varAcciones1);
			}
			else{
				$dataProvider = $model->Obtenerpcrcpadre(Yii::$app->request->post());	
			}
			
			$txtvalorador = $model->evaluados_id;

			return $this->render('_formpcrcpadre',[
				'model'=>$model,
				'dataProvider'=>$dataProvider,
				'sessiones'=>$sessiones,
				'txtvalorador' => $txtvalorador,
				'varArbolPadre' => $varArbolPadre,
				]);
		}

}
