<?php

namespace app\Controllers;

use Yii;
use app\models\Reglanegocio;
use app\models\ReglaNegocioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReglaNegocioController implements the CRUD actions for Reglanegocio model.
 */
class ReglanegocioController extends Controller {

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

    /**
     * Lists all Reglanegocio models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ReglaNegocioSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Reglanegocio model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Reglanegocio model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Reglanegocio();
        if ($model->load(Yii::$app->request->post())) {

            $pcrc = $this->dividirCadena($model->rn, ",");
            $cliente = $this->dividirCadena($model->cliente, "-");
            foreach ($pcrc as $key) {
                $modelAgregar = new Reglanegocio();
                $modelAgregar->rn = $key;
                $modelAgregar->pcrc = $model->pcrc;
                $modelAgregar->cliente = $cliente['1'];
                $modelAgregar->tipo_regla = $model->tipo_regla;
                $modelAgregar->cod_industria = $model->cod_industria;
                $modelAgregar->cod_institucion = $model->cod_institucion;
                $modelAgregar->promotores = $model->promotores;
                $modelAgregar->neutros = $model->neutros;
                $modelAgregar->detractores = $model->detractores;
                $modelAgregar->encu_mes = $model->encu_mes;
                $modelAgregar->encu_diarias = $model->encu_diarias;
                $modelAgregar->tramo1 = $model->tramo1;
                $modelAgregar->tramo2 = $model->tramo2;
                $modelAgregar->tramo3 = $model->tramo3;
                $modelAgregar->tramo4 = $model->tramo4;
                $modelAgregar->tramo5 = $model->tramo5;   
                $modelAgregar->tramo6 = $model->tramo6;   
                $modelAgregar->tramo7 = $model->tramo7;   
                $modelAgregar->tramo8 = $model->tramo8;  
                $modelAgregar->tramo9 = $model->tramo9;    
                $modelAgregar->tramo10 = $model->tramo10;    
                $modelAgregar->tramo11 = $model->tramo11;    
                $modelAgregar->tramo12 = $model->tramo12;    
                $modelAgregar->tramo13 = $model->tramo13;    
                $modelAgregar->tramo14 = $model->tramo14;    
                $modelAgregar->tramo15 = $model->tramo15;  
                $modelAgregar->tramo16 = $model->tramo16;  
                $modelAgregar->tramo17 = $model->tramo17;   
                $modelAgregar->tramo18 = $model->tramo18;  
                $modelAgregar->tramo19 = $model->tramo19;  
                $modelAgregar->tramo20 = $model->tramo20;  
                $modelAgregar->tramo21 = $model->tramo21;   
                $modelAgregar->tramo22 = $model->tramo22;  
                $modelAgregar->tramo23 = $model->tramo23;  
                $modelAgregar->tramo24 = $model->tramo24;
                $modelAgregar->correos_notificacion = $model->correos_notificacion;
                $modelAgregar->id_formulario = $model->id_formulario;
                $modelAgregar->rango_encuestas = '1';
                $modelAgregar->save();
                    
            }
            return $this->redirect(['index', 'model' => $model]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Reglanegocio model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $this->actualizarDatosReglaNegocio($model);
            return $this->redirect(['index', 'model' => $model]);
        } else {
            /*23-02-2016 -> Envio el codigo de institucion e industria para 
            tener una mejor validacion y  buscar RN que tengan el
            el mismo nombre pero codigo de industria e institucion distintos*/
            $registrosPares = $model->getRegistrosPares($model->pcrc, $model->cliente, $model->tipo_regla,$model->cod_industria, $model->cod_institucion);
            $model->rn = "";
            foreach ($registrosPares as $registro) {
                $model->rn.=$registro['rn'] . ',';
            }
            Yii::$app->session->set('idReglas', $registrosPares);
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Reglanegocio model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Reglanegocio model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Reglanegocio the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Reglanegocio::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
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

        $pcrcId = Yii::$app->request->post('pcrc');
        $model = new Reglanegocio();
        $datos = $model->getpadrecliente($pcrcId);
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
     * Metodo que permite actualizar los datos existentes en la base de datos
     *  @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function actualizarDatosReglaNegocio($model = null) {
        $rn = $this->dividirCadena($model->rn, ",");
        $cliente = $this->dividirCadena($model->cliente, "-");
        $reglas = Yii::$app->session->get('idReglas');
        for ($index = 0; $index < count($rn); $index++) {
            $modelAgregar = new Reglanegocio();
            for ($i = 0; $i < count($reglas); $i++) {
                if ($rn[$index] == $reglas[$i]['rn']) {
                    $modelAgregar = $this->findModel($reglas[$i]['id']);
                    unset($reglas[$i]);
                    break;
                }
            }
            $reglas = array_values($reglas);
            $modelAgregar->rn = $rn[$index];
            $modelAgregar->pcrc = $model->pcrc;
            $modelAgregar->cliente = $cliente['1'];
            $modelAgregar->tipo_regla = $model->tipo_regla;
            $modelAgregar->cod_industria = $model->cod_industria;
            $modelAgregar->cod_institucion = $model->cod_institucion;
            $modelAgregar->promotores = $model->promotores;
            $modelAgregar->neutros = $model->neutros;
            $modelAgregar->detractores = $model->detractores;
            $modelAgregar->encu_diarias = $model->encu_diarias;
            $modelAgregar->encu_mes = $model->encu_mes;
            $modelAgregar->tramo1 = $model->tramo1;
            $modelAgregar->tramo2 = $model->tramo2;
            $modelAgregar->tramo3 = $model->tramo3;
            $modelAgregar->tramo4 = $model->tramo4;
            $modelAgregar->tramo5 = $model->tramo5;   
            $modelAgregar->tramo6 = $model->tramo6;   
            $modelAgregar->tramo7 = $model->tramo7;   
            $modelAgregar->tramo8 = $model->tramo8;  
            $modelAgregar->tramo9 = $model->tramo9;    
            $modelAgregar->tramo10 = $model->tramo10;    
            $modelAgregar->tramo11 = $model->tramo11;    
            $modelAgregar->tramo12 = $model->tramo12;    
            $modelAgregar->tramo13 = $model->tramo13;     
            $modelAgregar->tramo14 = $model->tramo14;    
            $modelAgregar->tramo15 = $model->tramo15;  
            $modelAgregar->tramo16 = $model->tramo16;  
            $modelAgregar->tramo17 = $model->tramo17;   
            $modelAgregar->tramo18 = $model->tramo18;  
            $modelAgregar->tramo19 = $model->tramo19;  
            $modelAgregar->tramo20 = $model->tramo20;  
            $modelAgregar->tramo21 = $model->tramo21;   
            $modelAgregar->tramo22 = $model->tramo22;  
            $modelAgregar->tramo23 = $model->tramo23;  
            $modelAgregar->tramo24 = $model->tramo24; 
            $modelAgregar->rango_encuestas = $model->rango_encuestas;
            $modelAgregar->correos_notificacion = $model->correos_notificacion;
            $modelAgregar->id_formulario = $model->id_formulario;
            $modelAgregar->save();
        }
        for ($index1 = 0; $index1 < count($reglas); $index1++) {
            $modelEliminar = $this->findModel($reglas[$index1]['id']);
            $modelEliminar->delete();
        }
    }

    /**
     * Metodo que retorna la consulta para mostrar los formularios
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function actionGetformularios($search = null, $id = null) {

        $out = ['more' => false];
        if (!is_null($search)) {
            $data = \app\models\Formularios::find()
                    ->select(['id', 'text' => 'UPPER(name)'])
                    ->where('name LIKE "%' . $search . '%"')
                    ->orderBy('name')
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $ids = explode(',', $id);
            if (count($ids) > 0) {
                $data = \app\models\Formularios::find()
                        ->select(['id', 'text' => 'UPPER(name)'])
                        ->where('id = ' . $id)
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

}
