<?php

namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\filters\AccessControl;
use app\models\ControlTipoCortes;
use app\models\Tiposdecortes;
use app\models\Tipocortes;


class AdmincortesController extends \yii\web\Controller {

		public function behaviors(){
			return[
				'access' => [
						'class' => AccessControl::classname(),
						'only' => ['create', 'createcortes1', 'createcortes2', 'createcortes3',  'createcortes4', 'view', 'delete', 'update', 'update2'],
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
		*Accion que permite mostrar en pantalla el listados de los datos de cortes.
		*@return mixed
		*/
		public function actionIndex(){
			$model = new ControlTipoCortes();
			$sessiones = Yii::$app->user->identity->id;
			$dataProvider = $model->searchcortes(Yii::$app->request->post());			
			
			return $this->render('index',[
				'model'=>$model,
				'dataProvider'=>$dataProvider,
				]);
		}

		/**
		*Accion que permite ir a la pantalla para agregar los cortes y guardarlo en la BD.
		*@return mixed
		*/
		public function actionCreate(){
			$model = new Tipocortes();

			$formData = Yii::$app->request->post();		

	            if ($model->load($formData)) {
	                if ($model->save()) {
	                    Yii::$app->getsession()->setFlash('message','Registro agregado correctamente');
	                    return $this->redirect(['createcortes1', 'formData' => $formData]);
	                }
	                else
	                {
	                    Yii::$app->getsession()->setFlash('message','Fallo al agregar registro.');
	                }
	            }

			return $this->render('create', [
					'model' => $model,
				]);
		}


		/**
		*Accion que permite ir a la pantalla para agregar el corte1.
		*@return mixed
		*/
		public function actionCreatecortes1(){
			$model = new Tipocortes();
			$model1 = new Tiposdecortes();			

			$datoscontrol = Yii::$app->request->get('formData');

			$nombrecortes = $datoscontrol["Tipocortes"]["tipocortetc"];
			$nombredias = $datoscontrol["Tipocortes"]["diastc"];
			$fechai = $datoscontrol["Tipocortes"]["fechainiciotc"];
			$fechaf = $datoscontrol["Tipocortes"]["fechafintc"];
			$days = $datoscontrol["Tipocortes"]["cantdiastc"];
			$incluir = $datoscontrol["Tipocortes"]["incluir"];	

			$formData = Yii::$app->request->post();		

	            if ($model1->load($formData)) {
	                if ($model1->save()) {
	                    Yii::$app->getsession()->setFlash('message','Registro agregado correctamente');
	                    return $this->redirect(['createcortes2', 'formData' => $formData]);
	                }
	                else
	                {
	                    Yii::$app->getsession()->setFlash('message','Fallo al agregar registro.');
	                }
	            }

			
			return $this->render('_addcorte1', [
					'model' => $model,
					'model1' => $model1,
					'nombrecortes' => $nombrecortes,
					'nombredias' => $nombredias,
					'fechai' => $fechai,
					'fechaf' => $fechaf,
					'days' => $days,
					'incluir' => $incluir,
				]);
		}


		/**
		*Accion que permite ir a la pantalla para agregar el corte2.
		*@return mixed
		*/
		public function actionCreatecortes2(){
			$model = new Tipocortes();
			$model1 = new Tiposdecortes();			

			$datoscontrol = Yii::$app->request->get('formData');

			$id = $datoscontrol["Tiposdecortes"]["idtc"];

			$nombrecortes = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$nombredias = Yii::$app->db->createCommand("select diastc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$fechai = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$fechaf = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$days = Yii::$app->db->createCommand("select cantdiastc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$incluir = Yii::$app->db->createCommand("select incluir from tbl_tipocortes where idtc = '$id'")->queryScalar();


			$formData = Yii::$app->request->post();		

	            if ($model1->load($formData)) {
	                if ($model1->save()) {
	                    Yii::$app->getsession()->setFlash('message','Registro agregado correctamente');
	                    return $this->redirect(['createcortes3', 'formData' => $formData]);
	                }
	                else
	                {
	                    Yii::$app->getsession()->setFlash('message','Fallo al agregar registro.');
	                }
	            }
			
			return $this->render('_addcorte2', [
					'model' => $model,
					'model1' => $model1,
					'nombrecortes' => $nombrecortes,
					'nombredias' => $nombredias,
					'fechai' => $fechai,
					'fechaf' => $fechaf,
					'days' => $days,
					'incluir' => $incluir,
				]);
		}


		/**
		*Accion que permite ir a la pantalla para agregar el corte3.
		*@return mixed
		*/
		public function actionCreatecortes3(){
			$model = new Tipocortes();
			$model1 = new Tiposdecortes();			

			$datoscontrol = Yii::$app->request->get('formData');

			$id = $datoscontrol["Tiposdecortes"]["idtc"];

			$nombrecortes = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$nombredias = Yii::$app->db->createCommand("select diastc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$fechai = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$fechaf = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$days = Yii::$app->db->createCommand("select cantdiastc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$incluir = Yii::$app->db->createCommand("select incluir from tbl_tipocortes where idtc = '$id'")->queryScalar();


			$formData = Yii::$app->request->post();		

	            if ($model1->load($formData)) {
	                if ($model1->save()) {
	                    Yii::$app->getsession()->setFlash('message','Registro agregado correctamente');
	                    return $this->redirect(['createcortes4', 'formData' => $formData]);
	                }
	                else
	                {
	                    Yii::$app->getsession()->setFlash('message','Fallo al agregar registro.');
	                }
	            }
			
			return $this->render('_addcorte3', [
					'model' => $model,
					'model1' => $model1,
					'nombrecortes' => $nombrecortes,
					'nombredias' => $nombredias,
					'fechai' => $fechai,
					'fechaf' => $fechaf,
					'days' => $days,
					'incluir' => $incluir,
				]);
		}		


		/**
		*Accion que permite ir a la pantalla para agregar el corte4.
		*@return mixed
		*/
		public function actionCreatecortes4(){
			$model = new Tipocortes();
			$model1 = new Tiposdecortes();			

			$datoscontrol = Yii::$app->request->get('formData');

			$id = $datoscontrol["Tiposdecortes"]["idtc"];

			$nombrecortes = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$nombredias = Yii::$app->db->createCommand("select diastc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$fechai = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$fechaf = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$days = Yii::$app->db->createCommand("select cantdiastc from tbl_tipocortes where idtc = '$id'")->queryScalar();
			$incluir = Yii::$app->db->createCommand("select incluir from tbl_tipocortes where idtc = '$id'")->queryScalar();


			$formData = Yii::$app->request->post();		

	            if ($model1->load($formData)) {
	                if ($model1->save()) {
	                    Yii::$app->getsession()->setFlash('message','Registro agregado correctamente');
	                    return $this->redirect(['index']);
	                }
	                else
	                {
	                    Yii::$app->getsession()->setFlash('message','Fallo al agregar registro.');
	                }
	            }
			
			return $this->render('_addcorte4', [
					'model' => $model,
					'model1' => $model1,
					'nombrecortes' => $nombrecortes,
					'nombredias' => $nombredias,
					'fechai' => $fechai,
					'fechaf' => $fechaf,
					'days' => $days,
					'incluir' => $incluir,
				]);
		}	


		/**
		*Accion que permite ver el corte general y sus cortes
		*@return mixed
		*/
		public function actionView($idtc){
			$model = new Tipocortes();			
			$model2 = new Tiposdecortes();
			$dataProvider = null;
			$nameVal = null;
			$CorteID = $idtc;

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


			return $this->render('view', [
				'model' => $model,
				'dataProvider' => $dataProvider,
				'CorteID' => $CorteID,
				]);
		}


		/**
		*Accion que permite eliminar el corte general y sus diferentes cortes secundarios.
		*@return mixed
		*/
		public function actionDelete($idtc){
			$model = $this->findModel2($idtc);

			if ($model == null) {
				throw new NotFoundHttpException('El registro no existe.'); 
			}
			else
			{
				$model->delete();
				Tiposdecortes::deleteAll("idtc=:idtc", [":idtc" => $idtc]);
				return $this->redirect(['index']);
			}
		}
		protected function findModel2($idtc){
	        if (($model = Tipocortes::findOne($idtc)) !== null) {
	            return $model;
	        } else {
	            throw new NotFoundHttpException('The requested page does not exist.');
	        }
	    }


	    /**
		*Accion que permite realizar la modificacion del tipo de corte general
		*@return mixed
		*/
		public function actionUpdate($idtc){
			$model = new Tipocortes();
			$model2 = new Tiposdecortes();
			$dataProvider = null;
			$nameVal = null;

			$model = $this->findModel3($idtc);
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
			    Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!'));            

			    return $this->redirect(['index']);
			    
			} else {
			        
			        }

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
						$model->cantdiastc = $table->cantdiastc;
						$model->fechacreacion = $table->fechacreacion;
						$model->incluir = $table->incluir;

						$nameVal = Yii::$app->db->createCommand('select sum(cantdiastcs) from tbl_tipos_cortes where idtc = '.$idtc.'')->queryScalar();

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

			return $this->render('update', [
				'model' => $model,
				'dataProvider' => $dataProvider,
				'nameVal' => $nameVal,
				]);
		}

		protected function findModel3($idtc){
	        if (($model = Tipocortes::findOne($idtc)) !== null) {
	            return $model;
	        } else {
	            throw new NotFoundHttpException('The requested page does not exist.');
	        }
	    }


		/**
		*Accion que permite actualizar el corte seleccionado.
		*@return mixed
		*/
		public function actionUpdate2($idtcs){
			$model = new Tiposdecortes();
			$nameVal = null;
			$nameValDias = null;

			$model = $this->findModel1($idtcs);
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
			    Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!'));            
			    return $this->redirect(['index']);
			} else {
			        return $this->render('_formupdate2', [
			        	'model' => $model,
						'nameVal' => $nameVal,
						'nameValDias' => $nameValDias,
			        	]);
			        }




			return $this->render('_formupdate2', [
					'model' => $model,
					'nameVal' => $nameVal,
					'nameValDias' => $nameValDias,
				]);			
		}

		protected function findModel1($idtcs){
	        if (($model = Tiposdecortes::findOne($idtcs)) !== null) {
	            return $model;
	        } else {
	            throw new NotFoundHttpException('The requested page does not exist.');
	        }
	    }
}