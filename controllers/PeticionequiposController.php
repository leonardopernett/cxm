<?php
namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\filters\AccessControl;
use app\models\ControlDesvincular;
use app\models\ControlDesvinculacion;
use yii\db\Query;


	class PeticionequiposController extends \yii\web\Controller {

		public function behaviors(){
			return[
				'access' => [
						'class' => AccessControl::classname(),
						'only' => ['update','update2','desvincular','nodesvincular'],
						'rules' => [
							[
								'allow' => true,
								'roles' => ['@'],
								'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
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
		*
		*@return mixed
		*/
		public function actionIndex(){	
			$model = new ControlDesvincular();
			$model2 = new ControlDesvinculacion();

			$dataProvider = $model2->buscarpeticion(Yii::$app->request->post());

			return $this->render('index',[
					'model' => $model,
					'dataProvider' => $dataProvider,
				]);
		}


		public function actionUpdate($iddesvincular){
			$txtIdDesvin = $iddesvincular;

			$txtvalorador = Yii::$app->db->createCommand("select evaluados_id from tbl_control_desvincular where iddesvincular = $txtIdDesvin")->queryScalar(); 

			$txtCoordinador = Yii::$app->db->createCommand("select responsable from tbl_control_desvincular where iddesvincular = $txtIdDesvin")->queryScalar(); 

			$txtCorreo = Yii::$app->db->createCommand("select correo from tbl_control_desvincular where iddesvincular = $txtIdDesvin")->queryScalar(); 

			return $this->render('_formupdate',[
					'txtIdDesvin' => $txtIdDesvin,
					'txtvalorador' => $txtvalorador,
					'txtCoordinador' => $txtCoordinador,
					'txtCorreo' => $txtCorreo,
				]);
		}
		public function actionUpdate2($iddesvincular){
			$txtIdDesvin = $iddesvincular;

			$txtvalorador = Yii::$app->db->createCommand("select evaluados_id from tbl_control_desvincular where iddesvincular = $txtIdDesvin")->queryScalar(); 

			$txtCoordinador = Yii::$app->db->createCommand("select responsable from tbl_control_desvincular where iddesvincular = $txtIdDesvin")->queryScalar(); 

			$txtCorreo = Yii::$app->db->createCommand("select correo from tbl_control_desvincular where iddesvincular = $txtIdDesvin")->queryScalar(); 

			return $this->render('_formupdateneg',[
					'txtIdDesvin' => $txtIdDesvin,
					'txtvalorador' => $txtvalorador,
					'txtCoordinador' => $txtCoordinador,
					'txtCorreo' => $txtCorreo,
				]);
		}

		public function actionDesvincular(){
			(int)$txtidCoordi = Yii::$app->request->post("txtNamesC");
			(int)$txtidEvalua = Yii::$app->request->post("txtNamceT");
			(int)$txtDesvin = Yii::$app->request->post("txtDesvin");
			$txtEmail = Yii::$app->request->post("txtEmail");
			$varResultados = null;
			$varResultados1 = 1;
			
			$data = Yii::$app->db->createCommand("select * from tbl_control_procesos where evaluados_id = $txtidEvalua and  responsable = $txtidCoordi")->queryAll(); 


			foreach ($data as $key => $value) {
				$varId = $value['id'];

				Yii::$app->db->createCommand()->update('tbl_control_procesos',[
					                                'responsable' => $varResultados,
					                                'cant_valor' => $txtidCoordi,
					                                'anulado' => 1,
					                            ],'id ='.$varId.'')->execute(); 
			}

			Yii::$app->db->createCommand()->update('tbl_control_desvincular',[
					                                'anulado' => 1,
					                            ],'iddesvincular ='.$txtDesvin.'')->execute(); 

			$data2 = Yii::$app->db->createCommand("select * from tbl_control_params where evaluados_id = $txtidEvalua and  anulado = 0")->queryAll();

			foreach ($data2 as $key => $value) {
				$varId2 = $value['id'];

				Yii::$app->db->createCommand()->update('tbl_control_params',[
					                                'anulado' => 1,
					                            ],'id ='.$varId2.'')->execute(); 				
			}

			$message = "<html><body>";
                	$message .= "<h3>Notificación: El Técnico con el plan de valoración solicitado en la herramienta QA, ya ha sido desvinculado.</h3>";
                	$message .= "</body></html>";

         		Yii::$app->mailer->compose()
                        		->setTo($txtEmail)
                        		->setFrom(Yii::$app->params['email_satu_from'])
                        		->setSubject("Notificación, desvinculación de equipos")
                        		->setHtmlBody($message)
                        		->send();

			die(json_encode($varResultados1));
		}

		public function actionNodesvincular(){
			(int)$txtDesvin = Yii::$app->request->post("txtDesvin");
			$txtiddesvin = (int)$txtDesvin;
			$txtEmail = Yii::$app->request->post("txtEmail");
			$varResultados1 = 1;

			$txtmotivo = Yii::$app->db->createCommand("select motivo from tbl_control_desvincular where iddesvincular = $txtiddesvin")->queryScalar(); 
            
			Yii::$app->db->createCommand()->update('tbl_control_desvincular',[
					                                'anulado' => 1,
					                            ],'iddesvincular ='.$txtDesvin.'')->execute(); 

			
			$message = "<html><body>";
                	$message .= "<h3>Notificación: Se rechazo la desviculación de técnico ya que el motivo: (".$txtmotivo."), no cumple con las normas. Por favor comunicarse con el administrador de la herramienta. </h3>";
                	$message .= "</body></html>";

         		Yii::$app->mailer->compose()
                        		->setTo($txtEmail)
                        		->setFrom(Yii::$app->params['email_satu_from'])
                        		->setSubject("Notificación, rechazada la desvinculación de equipos")                        		
                        		->setHtmlBody($message)
                        		->send();

			die(json_encode($varResultados1));
		}

	}
?>