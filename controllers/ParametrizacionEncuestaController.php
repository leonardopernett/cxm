<?php

namespace app\controllers;

use Yii;
use app\models\ParametrizacionEncuesta;
use app\models\ParametrizacionEncuestaSearch;
use app\models\DetalleparametrizacionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ParametrizacionEncuestaController implements the CRUD actions for ParametrizacionEncuesta model.
 */
class ParametrizacionEncuestaController extends Controller {

    public function behaviors() {
        return [
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
     * Lists all ParametrizacionEncuesta models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ParametrizacionEncuestaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ParametrizacionEncuesta model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ParametrizacionEncuesta model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new ParametrizacionEncuesta();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ParametrizacionEncuesta model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ParametrizacionEncuesta model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $modelCategoriaGestion = \app\models\Categoriagestion::find()->where(["id_parametrizacion" => $model->id])->all();
        foreach ($modelCategoriaGestion as $value) {
            \app\models\Detalleparametrizacion::deleteAll(['id_categoriagestion' => $value->id]);
        }
        \app\models\Preguntas::deleteAll(['id_parametrizacion' => $model->id]);
        \app\models\Categoriagestion::deleteAll(['id_parametrizacion' => $model->id]);
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the ParametrizacionEncuesta model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ParametrizacionEncuesta the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ParametrizacionEncuesta::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Metodo que realiza la visualizacion de la parametrizacion encuesta
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function actionParametrizacionencuesta($id) {
        $model = $this->findModel($id);
        Yii::$app->session['rptModelParametrizacion'] = $this->findModel($id);
        $modelPregunta = \app\models\Preguntas::find()->where(["id_parametrizacion" => $model->id])->all();
        $modelPreguntaBase = new \app\models\Preguntas();
        $modelcategoriagestion = \app\models\Categoriagestion::find()->where(["id_parametrizacion" => $model->id])->all();
        $searchModelDetalle = new DetalleparametrizacionSearch();
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $datosgestion = [];
        foreach ($modelcategoriagestion as $value) {
            $queryParams['DetalleparametrizacionSearch']['id_categoriagestion'] = $value->id;
            $datosgestion[] = ['categoriagestion' => $value, 'dataProvider' => $searchModelDetalle->search($queryParams)];
        }
        $datos = new \stdClass();
        $datos->categorias = \yii\helpers\ArrayHelper::map(\app\models\Categorias::find()->all(), 'id', 'nombre');
        return $this->render('parametrizacionencuesta', [
                    'model' => $model,
                    'modelPregunta' => $modelPregunta,
                    'datos' => $datos,
                    'datosgestion' => $datosgestion,
                    'modelPreguntaBase' => $modelPreguntaBase,
        ]);
    }

    /**
     * Metodo que permite guardar las preguntas de la parametrizacion
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function actionGuardarparametrizacion($forma = 0) {
        $modelPregunta = \app\models\Preguntas::deleteAll(["id_parametrizacion" => Yii::$app->request->post('id')]);
        if (Yii::$app->request->post()) {
            for ($index = 1; $index <= 10; $index++) {
                $modelPregunta = new \app\models\Preguntas();
                $modelPregunta->categoria = (Yii::$app->request->post('categoria_p' . $index) != '') ? Yii::$app->request->post('categoria_p' . $index) : '';
                $modelPregunta->enunciado_pre = (Yii::$app->request->post('pregunta_' . $index) != '') ? Yii::$app->request->post('pregunta_' . $index) : '';
                $modelPregunta->id_parametrizacion = Yii::$app->request->post('id');
                $modelPregunta->pre_indicador = 'pregunta' . $index;
                $modelPregunta->save();
            }
            $model = Yii::$app->session['rptModelParametrizacion'];
            $model->save();
            if ($forma) {
                return $this->redirect(['index']);
            }
        }
    }

    /**
     * Metodo que realiza la visualizacion para seleccionar cliente y programa en parametrizacion encuesta
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function actionSelecparametrizacion() {
        $model = new ParametrizacionEncuesta();
        if ($model->load(Yii::$app->request->post())) {
            $cliente = $this->dividirCadena($model->cliente, '-');
            $model->cliente = $cliente['1'];
            $modelValidacion = ParametrizacionEncuesta::find()->where(["cliente" => $cliente["1"], "programa" => $model->programa])->all();
            if (count($modelValidacion) != 0) {
                $model = $modelValidacion['0'];
            }
            $model->save();
            return $this->redirect(['parametrizacionencuesta', 'id' => $model->id]);
        } else {
            return $this->render('selecdatos-parametrizacion', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Metodo que retorna la consulta para mostrar los arboles que son hijos.
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function actionGetarbolehoja($search = null, $id = null) {

        $out = ['more' => false];
        if (!is_null($search)) {
            $data = \app\models\Arboles::find()
                    ->select(['id', 'text' => 'UPPER(name)'])
                    ->where(["snhoja" => 1])
                    ->andWhere('name LIKE "%' . $search . '%"')
                    ->orderBy('name')
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $ids = explode(',', $id);
            if (count($ids) > 0) {
                $data = \app\models\Arboles::find()
                        ->select(['id', 'text' => 'UPPER(name)'])
                        ->where(["snhoja" => 1])
                        ->andWhere('id = ' . $id)
                        ->orderBy('name')
                        ->asArray()
                        ->all();
                $out['results'] = array_values($data);
            } else {
                $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
            }
        } else {
            $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
        }
        echo \yii\helpers\Json::encode($out);
        return;
    }

    /**
     * Metodo que retorna la consulta para mostrar los padres de los arboles q son hijos.
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function actionPadreclientedato() {

        $programaId = Yii::$app->request->post('programa');
        $model = new ParametrizacionEncuesta();
        $datos = $model->getpadrecliente($programaId);
        $datos[0]["value"] = $datos[0]["value"] . '-' . $datos[0]["id"];
        echo \yii\helpers\Json::encode($datos[0]);
        return;
    }

    /**
     * Metodo que permite dividir una cadena, dada la cadena y el parametro por el cual dividir
     *  @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function dividirCadena($cadena = null, $param = null) {
        return explode($param, $cadena);
    }

    /**
     * Metodo que elimina la categoria gestion y sus detalles.
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function actionEliminarcategorigestion($idparame, $categoriagestion) {
        \app\models\Detalleparametrizacion::deleteAll(['id_categoriagestion' => $categoriagestion]);
        $model = \app\models\Categoriagestion::findOne($categoriagestion);
        $model->delete();
        return $this->redirect(['parametrizacionencuesta', 'id' => $idparame]);
    }

}
