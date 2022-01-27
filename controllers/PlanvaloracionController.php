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


class PlanvaloracionController extends \yii\web\Controller {

		public function behaviors(){
			return[
				'access' => [
						'class' => AccessControl::classname(),
						'only' => ['view'],
						'rules' => [
							[
								'allow' => true,
								'roles' => ['@'],
								'matchCallback' => function() {
                            return Yii::$app->user->identity->isReportes();
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
			$model = new ControlProcesosEquipos();
			$sessiones = Yii::$app->user->identity->id;
			$dataProvider = $model->searchplan2($sessiones);
			$txtvalorado = $model->evaluados_id;			

			return $this->render('index',[
				'model'=>$model,
				'dataProvider'=>$dataProvider,
				'sessiones'=>$sessiones,
				'txtvalorado' => $txtvalorado,
				]);
		}


		/**
		*Accion que permite ver un usuario y sus dimensiones.
		*@return mixed
		*/
		public function actionView($id, $evaluados_id){
			$model = new \app\models\ControlProcesos();
			$model2 = new Controlparams();
			$dataProvider = null;
			$nameVal = null;

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
						$varName = Yii::$app->db->createCommand('select usua_nombre from tbl_usuarios where usua_id ='.$nameVal.'')->queryScalar();
						$model->salario = $table->salario;
						$model->tipo_corte = $table->tipo_corte;
						$txtNametc = $table->tipo_corte;
						$model->responsable = $table->responsable;
						$model->cant_valor = Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id = $nameVal and fechacreacion between '$fechainiC' and '$fechafinC'")->queryScalar();
						$varTotal = Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id = $nameVal and fechacreacion between '$fechainiC' and '$fechafinC'")->queryScalar();
						$model->Dedic_valora = $table->Dedic_valora;
						$varCant = $table->Dedic_valora;

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
				'txtNametc' => $txtNametc,
				'varName' => $varName,
				'varCant' => $varCant,
				'varTotal' => $varTotal,
				]);
		}		


}
