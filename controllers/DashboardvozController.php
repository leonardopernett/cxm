<?php

namespace app\controllers;

use Yii;
use DateTime;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\db\Query;
use app\models\VozSeleccion;
use app\models\ProcesosDirectores;
use app\models\ProcesosVolumendirector;
use app\models\ControlVolumenxclientedq;
use app\models\ControlVolumenxencuestasdq;

    class DashboardvozController extends \yii\web\Controller {

        public function behaviors(){
            return[
                'access' => [
                        'class' => AccessControl::classname(),
                        'only' => ['index','parametrizargerentes','listasciudad','detallevoz','enviarcorreo','exportar','graficamanuales','graficaencuestas','graficavaloracion','graficaindividual','graficaencuestasday','vocvideo'],
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

        public function actionIndex(){
            $model = new VozSeleccion();
            $varCiudad = null;
            $varArbol = null;

            $formData = Yii::$app->request->post();
            if ($model->load($formData)) {
                $varArbol = $model->arbol_id;
                $varCiudad = Yii::$app->db->createCommand("select a.name from tbl_arbols a inner join tbl_arbols aa on aa.arbol_id = a.id where aa.id = :varArbol and a.activo = 0")
                ->bindValue(':varArbol',$varArbol)
                ->queryScalar();

                return $this->redirect(array('detallevoz','varCodificacion'=>$varArbol));


            }

            return $this->render('index',[
                'model' => $model,
                'varCiudad' => $varCiudad,
                'varArbol' => $varArbol,
                ]);
        }

        public function actionParametrizargerentes(){
            $model = new VozSeleccion();
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");          
            $sessiones = Yii::$app->user->identity->id; 

            $form = Yii::$app->request->post();
            if ($model->load($form)) {
                $varCiudad = $model->ciudad;
                $varIdDiretor = $model->iddirectores;
                $varGerente = $model->gerentes;
                $varDocGerente = $model->documentogerentes;
                $varArbol = $model->arbol_id;

                Yii::$app->db->createCommand()->insert('tbl_voz_seleccion',[
                                                         'ciudad' => $varCiudad,
                                                         'iddirectores' => $varIdDiretor,
                                                         'gerentes' => $varGerente,
                                                         'documentogerentes' => $varDocGerente,
                                                         'arbol_id' => $varArbol,
                                                         'anulado' => $txtanulado,
                                                         'fechacreacion' => $txtfechacreacion,
                                                         'usua_id' => $sessiones,
                                                     ])->execute(); 

                return $this->redirect('index');
            }


            return $this->renderAjax('parametrizargerentes',[
                'model' => $model,
                ]);
        }

        public function actionListasciudad(){            
            $txtAnulado = 0;

            if ($txtCiudad = Yii::$app->request->post('id')) {                
                $txtControl = \app\models\Arboles::find()->distinct()
                            ->where(['like','name',$txtCiudad])
                            ->count();            

                if ($txtControl > 0) {
                    $txtLitadoDirectores = \app\models\ProcesosDirectores::find()->distinct()
                                ->where(['ciudad' => $txtCiudad])
                                ->andwhere(['anulado' => $txtAnulado])
                                ->all();

                    foreach ($txtLitadoDirectores as $key => $value) {
                        echo "<option value='" . $value->iddirectores. "'>" . $value->director_programa . "</option>";
                    }
                }else{
                    echo "<option>-</option>";
                }
            }else{
                    echo "<option>No hay datos</option>";
            }

        }

        public function actionDetallevoz($varCodificacion){
            $txtCodigo = $varCodificacion;
            $txtCiudad = "Actualizar información";
            $txtDirector = "Actualizar información";
            $txtArbol = "Actualizar información";

            $txtGerentes = Yii::$app->db->createCommand("select group_concat(distinct gerentes order by gerentes asc separator ', ') as concatenar from tbl_voz_seleccion vs where arbol_id = :txtCodigo and anulado = 0 ")
            ->bindValue(':txtCodigo',$txtCodigo)
            ->queryScalar();

            $query =  new Query;
            $query      ->select(['tbl_voz_seleccion.ciudad','tbl_procesos_directores.director_programa','tbl_arbols.name'])->distinct()
                                ->from('tbl_voz_seleccion')
                                ->join('LEFT OUTER JOIN', 'tbl_procesos_directores',
                                                'tbl_voz_seleccion.iddirectores = tbl_procesos_directores.iddirectores')
                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                'tbl_voz_seleccion.arbol_id = tbl_arbols.id')
                                ->where(['tbl_arbols.id' => $txtCodigo])
                                ->andwhere(['tbl_arbols.activo' => 0])
                                ->andwhere(['tbl_voz_seleccion.anulado' => 0]);
            $command = $query->createCommand();
            $listData = $command->queryAll();  

            foreach ($listData as $key => $value) {
                $txtCiudad = $value['ciudad'];
                $txtDirector = $value['director_programa'];
                $txtArbol = $value['name'];
            }


            return $this->render('detallevoz',[
                'txtCodigo' => $txtCodigo,
                'txtGerentes' => $txtGerentes,
                'txtCiudad' => $txtCiudad,
                'txtDirector' => $txtDirector,
                'txtArbol' => $txtArbol,
                ]);
        }

        public function actionParametrizarencuestas(){
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");

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

            $varServicios = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")
            ->queryAll();

            foreach ($varServicios as $key => $value) {
                $varIdServicios = $value['id'];

                if ($varIdServicios == '118' || $varIdServicios == '17') {
                    $querys =  new Query;
                    $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                ->from('tbl_tipocortes')
                                ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                        'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                ->where('tbl_grupo_cortes.idgrupocorte = 1')
                                ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                        $varIdCorte = $value['idtc'];
                        $varMesYear = $value['mesyear'];
                        $varDateBegin = $varMesYear.' 00:00:00';
                        $varMesYear1 = new DateTime($varMesYear);
                        $varMesYear1->modify('last day of this month');
                        $varDateLast = $varMesYear1->format('Y/m/d').' 23:59:59';

                        $querys = new Query;
                        $querys     ->select(["sum((select count(*) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varDateBegin and :varDateLast and tbl_base_satisfaccion.pcrc = tbl_arbols.id and tbl_base_satisfaccion.tipo_inbox in ('NORMAL'))) as Sumatotal"])->distinct()
                                    ->from('tbl_arbols')
                                    ->where('tbl_arbols.arbol_id = varIdServicios')
                                    ->andwhere("tbl_arbols.activo = 0")
                                    ->addParams([':varDateBegin' => $varDateBegin,':varDateLast' => $varDateLast,':varIdServicios' => $varIdServicios]);                                    
                        $command = $querys->createCommand();
                        $queryss = $command->queryScalar(); 

                        $varService = Yii::$app->db->createCommand("select count(*) from tbl_control_volumenxencuestas where idservicio = :varIdServicios and idtc = :varIdCorte and anuladovxe = 0")
                        ->bindValue(':varIdServicios',$varIdServicios)
                        ->bindValue(':varIdCorte',$varIdCorte)
                        ->queryScalar();

                        if ($varService == 0) {
                            Yii::$app->db->createCommand()->insert('tbl_control_volumenxencuestas',[
                                    'idservicio' => $varIdServicios,
                                    'idtc' => $varIdCorte,
                                    'mesyear' => $varMesYear,
                                    'cantidadvalorE' => $queryss,
                                    'fechacreacion' => $txtfechacreacion,
                                    'anuladovxe' => $txtanulado,
                                ])->execute();
                        }
                    }
                }else{
                    if($varIdServicios == '237' || $varIdServicios == '1358' || $varIdServicios == '105' || $varIdServicios == '8' || $varIdServicios == '99'){
                        $querys =  new Query;
                        $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 2')
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];
                            $varDateBegin = $varMesYear.' 00:00:00';
                            $varMesYear1 = new DateTime($varMesYear);
                            $varMesYear1->modify('last day of this month');
                            $varDateLast = $varMesYear1->format('Y/m/d').' 23:59:59';


                            $querys = new Query;
                            $querys     ->select(["sum((select count(*) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varDateBegin and :varDateLast and tbl_base_satisfaccion.pcrc = tbl_arbols.id and tbl_base_satisfaccion.tipo_inbox in ('NORMAL'))) as Sumatotal"])->distinct()
                                        ->from('tbl_arbols')
                                        ->where('tbl_arbols.arbol_id = :varIdServicios')
                                        ->andwhere("tbl_arbols.activo = 0")         
                                        ->addParams([':varDateBegin' => $varDateBegin,':varDateLast' => $varDateLast,':varIdServicios' => $varIdServicios]);                       
                            $command = $querys->createCommand();
                            $queryss = $command->queryScalar();

                            $varService = Yii::$app->db->createCommand("select count(*) from tbl_control_volumenxencuestas where idservicio = :varIdServicios and idtc = :varIdCorte and anuladovxe = 0")
                            ->bindValue(':varIdServicios',$varIdServicios)
                            ->bindValue(':varIdCorte',$varIdCorte)
                            ->queryScalar();

                            if ($varService == 0) {
                                Yii::$app->db->createCommand()->insert('tbl_control_volumenxencuestas',[
                                        'idservicio' => $varIdServicios,
                                        'idtc' => $varIdCorte,
                                        'mesyear' => $varMesYear,
                                        'cantidadvalorE' => $queryss,
                                        'fechacreacion' => $txtfechacreacion,
                                        'anuladovxe' => $txtanulado,
                                    ])->execute();
                            }
                        }
                    }else{
                        $querys =  new Query;
                        $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 3')
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];
                            $varDateBegin = $varMesYear.' 00:00:00';
                            $varMesYear1 = new DateTime($varMesYear);
                            $varMesYear1->modify('last day of this month');
                            $varDateLast = $varMesYear1->format('Y/m/d').' 23:59:59';

                            $querys = new Query;
                            $querys     ->select(["sum((select count(*) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varDateBegin' and :varDateLast and tbl_base_satisfaccion.pcrc = tbl_arbols.id and tbl_base_satisfaccion.tipo_inbox in ('NORMAL'))) as Sumatotal"])->distinct()
                                        ->from('tbl_arbols')
                                        ->where('tbl_arbols.arbol_id = :varIdServicios')
                                        ->andwhere("tbl_arbols.activo = 0")
                                        ->addParams([':varDateBegin' => $varDateBegin,':varDateLast' => $varDateLast,':varIdServicios' => $varIdServicios]);                                
                            $command = $querys->createCommand();
                            $queryss = $command->queryScalar();

                            $varService = Yii::$app->db->createCommand("select count(*) from tbl_control_volumenxencuestas where idservicio = :varIdServicios and idtc = :varIdCorte and anuladovxe = 0")
                            ->bindValue(':varIdServicios',$varIdServicios)
                            ->bindValue(':varIdCorte',$varIdCorte)
                            ->queryScalar();

                            if ($varService == 0) {
                                Yii::$app->db->createCommand()->insert('tbl_control_volumenxencuestas',[
                                        'idservicio' => $varIdServicios,
                                        'idtc' => $varIdCorte,
                                        'mesyear' => $varMesYear,
                                        'cantidadvalorE' => $queryss,
                                        'fechacreacion' => $txtfechacreacion,
                                        'anuladovxe' => $txtanulado,
                                    ])->execute();
                            }
                        }
                    }                    
                }
            } 
            return $this->redirect(['index']);           
     }


        public function actionParametrizarindicadores(){
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");

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

            $varServicios = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")->queryAll();

            foreach ($varServicios as $key => $value) {
                $varIdServicios = $value['id'];

                if ($varIdServicios == '118' || $varIdServicios == '17') {
                    $querys =  new Query;
                    $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                ->from('tbl_tipocortes')
                                ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                        'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                ->where('tbl_grupo_cortes.idgrupocorte = 1')
                                ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                        $varIdCorte = $value['idtc'];
                        $varMesYear = $value['mesyear'];
                        $varDateBegin1 = $varMesYear;
                        $varDateBegin = $varDateBegin1;
                        $varDateLast1 = date('Y-m-d',strtotime($varMesYear."+ 1 month"));
                        $varDateLast = $varDateLast1;

                        $querys = new Query;
                        $querys ->select(['tbl_dashboardcategorias.idcategoria','tbl_dashboardcategorias.nombre','tbl_dashboardservicios.clientecategoria','tbl_dashboardcategorias.fechacreacion'])
                                ->from('tbl_dashboardservicios')
                                ->join('LEFT OUTER JOIN', 'tbl_dashboardcategorias',
                                        'tbl_dashboardservicios.idservicios = tbl_dashboardcategorias.iddashservicio')
                                ->where('tbl_dashboardservicios.arbol_id = :varIdServicios')
                                ->andwhere(['tbl_dashboardcategorias.idcategorias' => 1])
                                ->andwhere(['like','tbl_dashboardcategorias.nombre','Satisfaccion'])
                                ->andwhere(['tbl_dashboardcategorias.anulado' => 0])
                                ->addParams([':varIdServicios'=>$varIdServicios]);
                                //->andwhere(['between','tbl_dashboardcategorias.fechacreacion',$varDateBegin,$varDateLast]);
                        $command = $querys->createCommand();
                        $query2 = $command->queryAll();

                        foreach ($query2 as $key => $value) {
                            $varIdCategoria = $value['idcategoria'];
                            $varNombreCateg = $value['nombre'];
                            $varClienteCate = $value['clientecategoria'];

                            $varFechas = Yii::$app->db->createCommand("select fechacreacion from tbl_dashboardcategorias where idcategoria = :varIdCategoria and anulado = 0")
                            ->bindValue(':varIdCategoria',$varIdCategoria)
                            ->queryScalar();

                            if ($varFechas >= '2020-01-01' && $varMesYear >= '2020-01-01') {
                                $txtContarCategorias = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = :varIdCategoria and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                ->bindValue(':varIdCategoria',$varIdCategoria)
                                ->bindValue(':varClienteCate',$varClienteCate)
                                ->bindValue(':varDateBegin',$varDateBegin)
                                ->bindValue(':varDateLast',$varDateLast)
                                ->queryScalar();


                                $txtContarGeneral = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = 1105 and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                ->bindValue(':varClienteCate',$varClienteCate)
                                ->bindValue(':varDateBegin',$varDateBegin)
                                ->bindValue(':varDateLast',$varDateLast)
                                ->queryScalar();

                                $varNameCiudad = Yii::$app->db->createCommand("select ciudadcategoria from tbl_dashboardcategorias where idcategoria = :varIdCategoria and clientecategoria like :varClienteCate and anulado = 0")
                                ->bindValue(':varIdCategoria',$varIdCategoria)
                                ->bindValue(':varClienteCate',$varClienteCate)
                                ->queryScalar();

                                if ($varNameCiudad == "BOGOTÁ") {
                                    $txtvarCity = 1;
                                    if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                        $txtRtaSatisfaccion = (round(($txtContarCategorias / $txtContarGeneral) * 100,1));
                                    }else{
                                        $txtRtaSatisfaccion = 0;
                                    }
                                }else{
                                    if ($varNameCiudad == "MEDELLÍN") {
                                        $txtvarCity = 2;
                                        if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                            $txtRtaSatisfaccion = (100 - (round(($txtContarCategorias / $txtContarGeneral) * 100,1)));
                                        }else{
                                            $txtRtaSatisfaccion = 0;
                                        }
                                    }
                                }

                                    Yii::$app->db->createCommand()->insert('tbl_voz_categorias',[
                                            'cantcategorias' => $txtRtaSatisfaccion,
                                            'nombrecategoria' => $varNombreCateg,
                                            'clientecategoria' => $varClienteCate,
                                            'idtc' => $varIdCorte,
                                            'arbol_id' => $varIdServicios,
                                            'mesyear' => $varMesYear,
                                            'indicador' => 1,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladovc' => $txtanulado,
                                            'year' => $varFechas,
                                            'idciudad' => $txtvarCity,
                                        ])->execute();
                            }
                        }
                    }
                }else{
                    if ($varIdServicios == '237' || $varIdServicios == '1358' || $varIdServicios == '105' || $varIdServicios == '8' || $varIdServicios == '99') {
                        $querys =  new Query;
                        $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 2')
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];
                            $varDateBegin1 = $varMesYear;
                            $varDateBegin = $varDateBegin1;
                            $varDateLast1 = date('Y-m-d',strtotime($varMesYear."+ 1 month"));
                            $varDateLast = $varDateLast1;

                            $querys = new Query;
                            $querys ->select(['tbl_dashboardcategorias.idcategoria','tbl_dashboardcategorias.nombre','tbl_dashboardservicios.clientecategoria','tbl_dashboardcategorias.fechacreacion'])
                                    ->from('tbl_dashboardservicios')
                                    ->join('LEFT OUTER JOIN', 'tbl_dashboardcategorias',
                                            'tbl_dashboardservicios.idservicios = tbl_dashboardcategorias.iddashservicio')
                                    ->where('tbl_dashboardservicios.arbol_id = :varIdServicios')
                                    ->andwhere(['tbl_dashboardcategorias.idcategorias' => 1])
                                    ->andwhere(['like','tbl_dashboardcategorias.nombre','Satisfaccion'])
                                    ->andwhere(['tbl_dashboardcategorias.anulado' => 0])
                                    ->addParams([':varIdServicios'=>$varIdServicios]);
                                    
                                    //->andwhere(['between','tbl_dashboardcategorias.fechacreacion',$varDateBegin,$varDateLast]);
                            $command = $querys->createCommand();
                            $query2 = $command->queryAll();

                            foreach ($query2 as $key => $value) {
                                $varIdCategoria = $value['idcategoria'];
                                $varNombreCateg = $value['nombre'];
                                $varClienteCate = $value['clientecategoria'];

                                $varFechas = Yii::$app->db->createCommand("select fechacreacion from tbl_dashboardcategorias where idcategoria = :varIdCategoria and anulado = 0")
                                ->bindValue(':varIdCategoria',$varIdCategoria)
                                ->queryScalar();

                                if ($varFechas >= '2020-01-01' && $varMesYear >= '2020-01-01'){
                                    $txtContarCategorias = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = :varIdCategoria and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                    ->bindValue(':varIdCategoria',$varIdCategoria)
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->queryScalar();


                                    $txtContarGeneral = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = 1105 and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->queryScalar();

                                    $varNameCiudad = Yii::$app->db->createCommand("select ciudadcategoria from tbl_dashboardcategorias where idcategoria = :varIdCategoria and clientecategoria like :varClienteCate and anulado = 0")
                                    ->bindValue(':varIdCategoria',$varIdCategoria)
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->queryScalar();

                                    if ($varNameCiudad == "BOGOTÁ") {
                                        $txtvarCity = 1;
                                        if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                            $txtRtaSatisfaccion = (100 - (round(($txtContarCategorias / $txtContarGeneral) * 100,1)));
                                        }else{
                                            $txtRtaSatisfaccion = 0;
                                        }
                                    }else{
                                        if ($varNameCiudad == "MEDELLÍN") {
                                            $txtvarCity = 2;
                                            if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                                $txtRtaSatisfaccion = (100 - (round(($txtContarCategorias / $txtContarGeneral) * 100,1)));
                                            }else{
                                                $txtRtaSatisfaccion = 0;
                                            }
                                        }
                                    }

                                    Yii::$app->db->createCommand()->insert('tbl_voz_categorias',[
                                            'cantcategorias' => $txtRtaSatisfaccion,
                                            'nombrecategoria' => $varNombreCateg,
                                            'clientecategoria' => $varClienteCate,
                                            'idtc' => $varIdCorte,
                                            'arbol_id' => $varIdServicios,
                                            'mesyear' => $varMesYear,
                                            'indicador' => 1,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladovc' => $txtanulado,
                                            'year' => $varFechas,
                                            'idciudad' => $txtvarCity,
                                        ])->execute();
                                }
                            }
                        }
                    }else{
                        $querys =  new Query;
                        $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 3')
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];
                            $varDateBegin1 = $varMesYear;
                            $varDateBegin = $varDateBegin1;
                            $varDateLast1 = date('Y-m-d',strtotime($varMesYear."+ 1 month"));
                            $varDateLast = $varDateLast1;

                            $querys = new Query;
                            $querys ->select(['tbl_dashboardcategorias.idcategoria','tbl_dashboardcategorias.nombre','tbl_dashboardservicios.clientecategoria','tbl_dashboardcategorias.fechacreacion'])
                                    ->from('tbl_dashboardservicios')
                                    ->join('LEFT OUTER JOIN', 'tbl_dashboardcategorias',
                                            'tbl_dashboardservicios.idservicios = tbl_dashboardcategorias.iddashservicio')
                                    ->where('tbl_dashboardservicios.arbol_id = :varIdServicios')
                                    ->andwhere(['tbl_dashboardcategorias.idcategorias' => 1])
                                    ->andwhere(['like','tbl_dashboardcategorias.nombre','Satisfaccion'])
                                    ->andwhere(['tbl_dashboardcategorias.anulado' => 0])
                                    ->addParams([':varIdServicios'=>$varIdServicios]);
                                    //->andwhere(['between','tbl_dashboardcategorias.fechacreacion',$varDateBegin,$varDateLast]);
                            $command = $querys->createCommand();
                            $query2 = $command->queryAll();

                            foreach ($query2 as $key => $value) {
                                $varIdCategoria = $value['idcategoria'];
                                $varNombreCateg = $value['nombre'];
                                $varClienteCate = $value['clientecategoria'];       

                                $varFechas = Yii::$app->db->createCommand("select fechacreacion from tbl_dashboardcategorias where idcategoria = :varIdCategoria and anulado = 0")
                                ->bindValue(':varIdCategoria',$varIdCategoria)
                                ->queryScalar();

                                if ($varFechas >= '2020-01-01' && $varMesYear >= '2020-01-01'){
                                    $txtContarCategorias = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = :varIdCategoria and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                    ->bindValue(':varIdCategoria',$varIdCategoria)
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->queryScalar();

                                    $txtContarGeneral = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = 1114 and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->queryScalar();

                                    $varNameCiudad = Yii::$app->db->createCommand("select ciudadcategoria from tbl_dashboardcategorias where idcategoria = :varIdCategoria and clientecategoria like :varClienteCate and anulado = 0")
                                    ->bindValue(':varIdCategoria',$varIdCategoria)
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->queryScalar();

                                    if ($varNameCiudad == "BOGOTÁ") {
                                        $txtvarCity = 1;
                                        if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                            $txtRtaSatisfaccion = (round(($txtContarCategorias / $txtContarGeneral) * 100,1));
                                        }else{
                                            $txtRtaSatisfaccion = 0;
                                        }
                                    }else{
                                        if ($varNameCiudad == "MEDELLÍN") {
                                            $txtvarCity = 2;
                                            if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                                $txtRtaSatisfaccion = (100 - (round(($txtContarCategorias / $txtContarGeneral) * 100,1)));
                                            }else{
                                                $txtRtaSatisfaccion = 0;
                                            }
                                        }
                                    }

                                    Yii::$app->db->createCommand()->insert('tbl_voz_categorias',[
                                            'cantcategorias' => $txtRtaSatisfaccion,
                                            'nombrecategoria' => $varNombreCateg,
                                            'clientecategoria' => $varClienteCate,
                                            'idtc' => $varIdCorte,
                                            'arbol_id' => $varIdServicios,
                                            'mesyear' => $varMesYear,
                                            'indicador' => 1,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladovc' => $txtanulado,
                                            'year' => $varFechas,
                                            'idciudad' => $txtvarCity,
                                        ])->execute();
                                }  
                            }
                        }
                    }
                }
            }
            return $this->redirect(['index']); 
        }


        public function actionParametrizarindicadores2(){
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");

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

            $varServicios = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")->queryAll();

            foreach ($varServicios as $key => $value) {
                $varIdServicios = $value['id'];

                if ($varIdServicios == '118' || $varIdServicios == '17') {
                    $querys =  new Query;
                    $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                ->from('tbl_tipocortes')
                                ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                        'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                ->where('tbl_grupo_cortes.idgrupocorte = 1')
                                ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                        $varIdCorte = $value['idtc'];
                        $varMesYear = $value['mesyear'];
                        $varDateBegin1 = $varMesYear;
                        $varDateBegin = $varDateBegin1;
                        $varDateLast1 = date('Y-m-d',strtotime($varMesYear."+ 1 month"));
                        $varDateLast = $varDateLast1;

                        $querys = new Query;
                        $querys ->select(['tbl_dashboardcategorias.idcategoria','tbl_dashboardcategorias.nombre','tbl_dashboardservicios.clientecategoria','tbl_dashboardcategorias.fechacreacion'])
                                ->from('tbl_dashboardservicios')
                                ->join('LEFT OUTER JOIN', 'tbl_dashboardcategorias',
                                        'tbl_dashboardservicios.idservicios = tbl_dashboardcategorias.iddashservicio')
                                ->where('tbl_dashboardservicios.arbol_id = :varIdServicios')
                                ->andwhere(['tbl_dashboardcategorias.idcategorias' => 1])
                                ->andwhere(['like','tbl_dashboardcategorias.nombre','Solución'])
                                ->andwhere(['tbl_dashboardcategorias.anulado' => 0])
                                ->addParams([':varIdServicios'=>$varIdServicios]);
                                //->andwhere(['between','tbl_dashboardcategorias.fechacreacion',$varDateBegin,$varDateLast]);
                        $command = $querys->createCommand();
                        $query2 = $command->queryAll();

                        foreach ($query2 as $key => $value) {
                            $varIdCategoria = $value['idcategoria'];
                            $varNombreCateg = $value['nombre'];
                            $varClienteCate = $value['clientecategoria'];

                            $varFechas = Yii::$app->db->createCommand("select fechacreacion from tbl_dashboardcategorias where idcategoria = :varIdCategoria and anulado = 0")
                            ->bindValue(':varIdCategoria',$varIdCategoria)
                            ->queryScalar();

                            if ($varFechas >= '2020-01-01' && $varMesYear >= '2020-01-01'){
                                $txtContarCategorias = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = :varIdCategoria and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                ->bindValue(':varIdCategoria',$varIdCategoria)
                                ->bindValue(':varClienteCate',$varClienteCate)
                                ->bindValue(':varDateBegin',$varDateBegin)
                                ->bindValue(':varDateLast',$varDateLast)
                                ->queryScalar();


                                $txtContarGeneral = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = 1105 and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                ->bindValue(':varClienteCate',$varClienteCate)
                                ->bindValue(':varDateBegin',$varDateBegin)
                                ->bindValue(':varDateLast',$varDateLast)
                                ->queryScalar();

                                $varNameCiudad = Yii::$app->db->createCommand("select ciudadcategoria from tbl_dashboardcategorias where idcategoria = :varIdCategoria and clientecategoria like :varClienteCate and anulado = 0")
                                ->bindValue(':varIdCategoria',$varIdCategoria)
                                ->bindValue(':varClienteCate',$varClienteCate)
                                ->queryScalar();

                                if ($varNameCiudad == "BOGOTÁ") {
                                    $txtvarCity = 1;
                                    if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                        $txtRtaSatisfaccion = (round(($txtContarCategorias / $txtContarGeneral) * 100,1));
                                    }else{
                                        $txtRtaSatisfaccion = 0;
                                    }
                                }else{
                                    if ($varNameCiudad == "MEDELLÍN") {
                                        $txtvarCity = 2;
                                        if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                            $txtRtaSatisfaccion = (100 - (round(($txtContarCategorias / $txtContarGeneral) * 100,1)));
                                        }else{
                                            $txtRtaSatisfaccion = 0;
                                        }
                                    }
                                }
                                    Yii::$app->db->createCommand()->insert('tbl_voz_categorias',[
                                            'cantcategorias' => $txtRtaSatisfaccion,
                                            'nombrecategoria' => $varNombreCateg,
                                            'clientecategoria' => $varClienteCate,
                                            'idtc' => $varIdCorte,
                                            'arbol_id' => $varIdServicios,
                                            'mesyear' => $varMesYear,
                                            'indicador' => 2,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladovc' => $txtanulado,
                                            'year' => $varFechas,
                                            'idciudad' => $txtvarCity,
                                        ])->execute();

                            }                                

                        }
                    }
                }else{
                    if ($varIdServicios == '237' || $varIdServicios == '1358' || $varIdServicios == '105' || $varIdServicios == '8' || $varIdServicios == '99') {
                        $querys =  new Query;
                        $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 2')
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];
                            $varDateBegin1 = $varMesYear;
                            $varDateBegin = $varDateBegin1;
                            $varDateLast1 = date('Y-m-d',strtotime($varMesYear."+ 1 month"));
                            $varDateLast = $varDateLast1;

                            $querys = new Query;
                            $querys ->select(['tbl_dashboardcategorias.idcategoria','tbl_dashboardcategorias.nombre','tbl_dashboardservicios.clientecategoria','tbl_dashboardcategorias.fechacreacion'])
                                    ->from('tbl_dashboardservicios')
                                    ->join('LEFT OUTER JOIN', 'tbl_dashboardcategorias',
                                            'tbl_dashboardservicios.idservicios = tbl_dashboardcategorias.iddashservicio')
                                    ->where('tbl_dashboardservicios.arbol_id = :varIdServicios')
                                    ->andwhere(['tbl_dashboardcategorias.idcategorias' => 1])
                                    ->andwhere(['like','tbl_dashboardcategorias.nombre','Solución'])
                                    ->andwhere(['tbl_dashboardcategorias.anulado' => 0])
                                    ->addParams([':varIdServicios'=>$varIdServicios]);
                                    //->andwhere(['between','tbl_dashboardcategorias.fechacreacion',$varDateBegin,$varDateLast]);
                            $command = $querys->createCommand();
                            $query2 = $command->queryAll();

                            foreach ($query2 as $key => $value) {
                                $varIdCategoria = $value['idcategoria'];
                                $varNombreCateg = $value['nombre'];
                                $varClienteCate = $value['clientecategoria'];

                                $varFechas = Yii::$app->db->createCommand("select fechacreacion from tbl_dashboardcategorias where idcategoria = :varIdCategoria and anulado = 0")
                                ->bindValue(':varIdCategoria',$varIdCategoria)
                                ->queryScalar();

                                if ($varFechas >= '2020-01-01' && $varMesYear >= '2020-01-01'){
                                    $txtContarCategorias = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = :varIdCategoria and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                    ->bindValue(':varIdCategoria',$varIdCategoria)
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->queryScalar();

                                    $txtContarGeneral = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = 1105 and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->queryScalar();

                                    $varNameCiudad = Yii::$app->db->createCommand("select ciudadcategoria from tbl_dashboardcategorias where idcategoria = :varIdCategoria and clientecategoria like :varClienteCate and anulado = 0")
                                    ->bindValue(':varIdCategoria',$varIdCategoria)
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->queryScalar();

                                    if ($varNameCiudad == "BOGOTÁ") {
                                        $txtvarCity = 1;
                                        if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                            $txtRtaSatisfaccion = (round(($txtContarCategorias / $txtContarGeneral) * 100,1));
                                        }else{
                                            $txtRtaSatisfaccion = 0;
                                        }
                                    }else{
                                        if ($varNameCiudad == "MEDELLÍN") {
                                            $txtvarCity = 2;
                                            if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                                $txtRtaSatisfaccion = (100 - (round(($txtContarCategorias / $txtContarGeneral) * 100,1)));
                                            }else{
                                                $txtRtaSatisfaccion = 0;
                                            }
                                        }
                                    }

                                        Yii::$app->db->createCommand()->insert('tbl_voz_categorias',[
                                                'cantcategorias' => $txtRtaSatisfaccion,
                                                'nombrecategoria' => $varNombreCateg,
                                                'clientecategoria' => $varClienteCate,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 2,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladovc' => $txtanulado,
                                                'year' => $varFechas,
                                                'idciudad' => $txtvarCity,
                                            ])->execute();

                                }

                            }
                        }
                    }else{
                        $querys =  new Query;
                        $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 3')
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];
                            $varDateBegin1 = $varMesYear;
                            $varDateBegin = $varDateBegin1;
                            $varDateLast1 = date('Y-m-d',strtotime($varMesYear."+ 1 month"));
                            $varDateLast = $varDateLast1;

                            $querys = new Query;
                            $querys ->select(['tbl_dashboardcategorias.idcategoria','tbl_dashboardcategorias.nombre','tbl_dashboardservicios.clientecategoria','tbl_dashboardcategorias.fechacreacion'])
                                    ->from('tbl_dashboardservicios')
                                    ->join('LEFT OUTER JOIN', 'tbl_dashboardcategorias',
                                            'tbl_dashboardservicios.idservicios = tbl_dashboardcategorias.iddashservicio')
                                    ->where('tbl_dashboardservicios.arbol_id = :varIdServicios')
                                    ->andwhere(['tbl_dashboardcategorias.idcategorias' => 1])
                                    ->andwhere(['like','tbl_dashboardcategorias.nombre','Solución'])
                                    ->andwhere(['tbl_dashboardcategorias.anulado' => 0])
                                    ->addParams([':varIdServicios'=>$varIdServicios]);
                                    //->andwhere(['between','tbl_dashboardcategorias.fechacreacion',$varDateBegin,$varDateLast]);
                            $command = $querys->createCommand();
                            $query2 = $command->queryAll();

                            foreach ($query2 as $key => $value) {
                                $varIdCategoria = $value['idcategoria'];
                                $varNombreCateg = $value['nombre'];
                                $varClienteCate = $value['clientecategoria'];

                                $varFechas = Yii::$app->db->createCommand("select fechacreacion from tbl_dashboardcategorias where idcategoria = :varIdCategoria and anulado = 0")
                                ->bindValue(':varIdCategoria',$varIdCategoria)
                                ->queryScalar();

                                if ($varFechas >= '2020-01-01' && $varMesYear >= '2020-01-01'){
                                    $txtContarCategorias = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = :varIdCategoria and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                    ->bindValue(':varIdCategoria',$varIdCategoria)
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->queryScalar();


                                    $txtContarGeneral = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where idcategoria = 1114 and servicio like '%:varClienteCate%' and fechallamada between ':varDateBegin 05:00:00' and ':varDateLast 05:00:00' and anulado = 0")
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->queryScalar();

                                    $varNameCiudad = Yii::$app->db->createCommand("select ciudadcategoria from tbl_dashboardcategorias where idcategoria = :varIdCategoria and clientecategoria like :varClienteCate and anulado = 0")
                                    ->bindValue(':varIdCategoria',$varIdCategoria)
                                    ->bindValue(':varClienteCate',$varClienteCate)
                                    ->queryScalar();

                                    if ($varNameCiudad == "BOGOTÁ") {
                                        $txtvarCity = 1;
                                        if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                            $txtRtaSatisfaccion = (round(($txtContarCategorias / $txtContarGeneral) * 100,1));
                                        }else{
                                            $txtRtaSatisfaccion = 0;
                                        }
                                    }else{
                                        if ($varNameCiudad == "MEDELLÍN") {
                                            $txtvarCity = 2;
                                            if ($txtContarCategorias != 0 && $txtContarGeneral != 0) {
                                                $txtRtaSatisfaccion = (100 - (round(($txtContarCategorias / $txtContarGeneral) * 100,1)));
                                            }else{
                                                $txtRtaSatisfaccion = 0;
                                            }
                                        }
                                    }

                                        Yii::$app->db->createCommand()->insert('tbl_voz_categorias',[
                                                'cantcategorias' => $txtRtaSatisfaccion,
                                                'nombrecategoria' => $varNombreCateg,
                                                'clientecategoria' => $varClienteCate,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 2,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladovc' => $txtanulado,
                                                'year' => $varFechas,
                                                'idciudad' => $txtvarCity,
                                            ])->execute();

                                }
                            }
                        }
                    }
                }
            }
            return $this->redirect(['index']); 
        }          

        public function actionParametrizarindicadores3(){
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");

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

            $varServicios = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")->queryAll();

            foreach ($varServicios as $key => $value) {
                $varIdServicios = $value['id'];

                if ($varIdServicios == '118' || $varIdServicios == '17') {
                    $querys =  new Query;
                    $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                ->from('tbl_tipocortes')
                                ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                        'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                ->where('tbl_grupo_cortes.idgrupocorte = 1')
                                ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                        $varIdCorte = $value['idtc'];
                        $varMesYear = $value['mesyear'];
                        $varDateBegin = $varMesYear.' 00:00:00';
                        $varMesYear1 = new DateTime($varMesYear);
                        $varMesYear1->modify('last day of this month');
                        $varDateLast = $varMesYear1->format('Y/m/d').' 23:59:59';

                        $querys1 =  new Query;
                        $querys1    ->select(['tbl_parametrizacion_encuesta.programa'])->distinct()
                                    ->from('tbl_preguntas')
                                    ->join('LEFT OUTER JOIN', 'tbl_parametrizacion_encuesta',
                                            'tbl_preguntas.id_parametrizacion = tbl_parametrizacion_encuesta.id')
                                    ->join('LEFT OUTER JOIN', 'tbl_categorias',
                                            'tbl_preguntas.categoria = tbl_categorias.id')
                                    ->where("tbl_parametrizacion_encuesta.cliente = :varIdServicios")
                                    ->andwhere("tbl_categorias.id = 1")
                                    ->addParams([':varIdServicios'=>$varIdServicios]);                    
                        $command1 = $querys1->createCommand();
                        $query1 = $command1->queryAll();

                        foreach ($query1 as $key => $value) {
                            $txtIdPrograma = $value['programa'];

                            $varVerificar = Yii::$app->db->createCommand("select count(*) from tbl_arbols where id = :txtIdPrograma  and activo = 0")
                            ->bindValue(':txtIdPrograma',$txtIdPrograma)
                            ->queryScalar();

                            if ($varVerificar != '0') {
                                $varMax = Yii::$app->db->createCommand("select max(pregunta1) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varDateBegin and :varDateLast and tbl_base_satisfaccion.pcrc = :txtIdPrograma")
                                ->bindValue(':varDateBegin',$varDateBegin)
                                ->bindValue(':varDateLast',$varDateLast)
                                ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                ->queryScalar();


                                if ($varMax > 5) {
                                    $varContarNSatu = Yii::$app->db->createCommand("select (round(((((select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.pregunta1 in (8,9,10)) - (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.pregunta1 in (0,1,2,3,4,5))) / (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast))*100),2)) as VarNSatu from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on  p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where a.id = :txtIdPrograma and c.id = 1 and a.activo = 0 ")
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();

                                    if ($varContarNSatu != null) {
                                        Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                            'cantencuestas' => $varContarNSatu,
                                            'nombreencuestas' => 'NSatisfacción',
                                            'programa_id' => $txtIdPrograma,
                                            'idtc' => $varIdCorte,
                                            'arbol_id' => $varIdServicios,
                                            'mesyear' => $varMesYear,
                                            'indicador' => 3,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladove' => $txtanulado,
                                        ])->execute();
                                    } 

                                }else{
                                    $varContarSatu = Yii::$app->db->createCommand("select (round(((select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.pregunta1 in (4,5)) / (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 1 and a.activo = 0")
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();

                                    if ($varContarSatu != null) {
                                        Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                            'cantencuestas' => $varContarSatu,
                                            'nombreencuestas' => 'Satisfacción',
                                            'programa_id' => $txtIdPrograma,
                                            'idtc' => $varIdCorte,
                                            'arbol_id' => $varIdServicios,
                                            'mesyear' => $varMesYear,
                                            'indicador' => 1,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladove' => $txtanulado,
                                        ])->execute();
                                    }                                

                                    $varContarInSatu = Yii::$app->db->createCommand("select (round(((select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.pregunta1 in (1,2)) / (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 1 and a.activo = 0")
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();

                                    if ($varContarInSatu != null) {
                                        Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                            'cantencuestas' => $varContarInSatu,
                                            'nombreencuestas' => 'Insatisfacción',
                                            'programa_id' => $txtIdPrograma,
                                            'idtc' => $varIdCorte,
                                            'arbol_id' => $varIdServicios,
                                            'mesyear' => $varMesYear,
                                            'indicador' => 2,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladove' => $txtanulado,
                                        ])->execute();
                                    }
                                }
                            }
                        }
                    }
                }else{
                    if ($varIdServicios == '237' || $varIdServicios == '1358' || $varIdServicios == '105' || $varIdServicios == '8' || $varIdServicios == '99') {
                        $querys =  new Query;
                        $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 2')
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];
                            $varDateBegin = $varMesYear.' 00:00:00';
                            $varMesYear1 = new DateTime($varMesYear);
                            $varMesYear1->modify('last day of this month');
                            $varDateLast = $varMesYear1->format('Y/m/d').' 23:59:59';

                            $querys1 =  new Query;
                            $querys1    ->select(['tbl_parametrizacion_encuesta.programa'])->distinct()
                                        ->from('tbl_preguntas')
                                        ->join('LEFT OUTER JOIN', 'tbl_parametrizacion_encuesta',
                                                'tbl_preguntas.id_parametrizacion = tbl_parametrizacion_encuesta.id')
                                        ->join('LEFT OUTER JOIN', 'tbl_categorias',
                                                'tbl_preguntas.categoria = tbl_categorias.id')
                                        ->where("tbl_parametrizacion_encuesta.cliente = :varIdServicios")
                                        ->andwhere("tbl_categorias.id = 1")
                                        ->addParams([':varIdServicios'=>$varIdServicios]);                    
                            $command1 = $querys1->createCommand();
                            $query1 = $command1->queryAll();

                            foreach ($query1 as $key => $value) {
                                $txtIdPrograma = $value['programa'];

                                $varVerificar = Yii::$app->db->createCommand("select count(*) from tbl_arbols where id = :txtIdPrograma  and activo = 0")
                                ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                ->queryScalar();

                                if ($varVerificar != '0') {
                                    $varMax = Yii::$app->db->createCommand("select max(pregunta1) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varDateBegin and :varDateLast and tbl_base_satisfaccion.pcrc = :txtIdPrograma")
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();


                                    if ($varMax > 5) {
                                        $varContarNSatu = Yii::$app->db->createCommand("select (round(((((select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between '$varDateBegin' and '$varDateLast' and bs.pregunta1 in (8,9,10)) - (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.pregunta1 in (0,1,2,3,4,5))) / (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast))*100),2)) as VarNSatu from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on  p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where a.id = :txtIdPrograma and c.id = 1 and a.activo = 0 ")
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarNSatu != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarNSatu,
                                                'nombreencuestas' => 'NSatisfacción',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 3,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        } 

                                    }else{

                                        $varContarSatu = Yii::$app->db->createCommand("select (round(((select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.pregunta1 in (4,5)) / (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 1 and a.activo = 0")
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarSatu != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarSatu,
                                                'nombreencuestas' => 'Satisfacción',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 1,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        }                                

                                        $varContarInSatu = Yii::$app->db->createCommand("select (round(((select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.pregunta1 in (1,2)) / (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 1 and a.activo = 0")
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarInSatu != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarInSatu,
                                                'nombreencuestas' => 'Insatisfacción',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 2,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        }
                                    }

                                }
                            }
                        }
                    }else{
                        $querys =  new Query;
                        $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 3')
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];
                            $varDateBegin = $varMesYear.' 00:00:00';
                            $varMesYear1 = new DateTime($varMesYear);
                            $varMesYear1->modify('last day of this month');
                            $varDateLast = $varMesYear1->format('Y/m/d').' 23:59:59';

                            $querys1 =  new Query;
                            $querys1    ->select(['tbl_parametrizacion_encuesta.programa'])->distinct()
                                        ->from('tbl_preguntas')
                                        ->join('LEFT OUTER JOIN', 'tbl_parametrizacion_encuesta',
                                                'tbl_preguntas.id_parametrizacion = tbl_parametrizacion_encuesta.id')
                                        ->join('LEFT OUTER JOIN', 'tbl_categorias',
                                                'tbl_preguntas.categoria = tbl_categorias.id')
                                        ->where("tbl_parametrizacion_encuesta.cliente = :varIdServicios")
                                        ->andwhere("tbl_categorias.id = 1")
                                        ->addParams([':varIdServicios'=>$varIdServicios]);                    
                            $command1 = $querys1->createCommand();
                            $query1 = $command1->queryAll();

                            foreach ($query1 as $key => $value) {
                                $txtIdPrograma = $value['programa'];

                               $varVerificar = Yii::$app->db->createCommand("select count(*) from tbl_arbols where id = :txtIdPrograma  and activo = 0")
                               ->bindValue(':txtIdPrograma',$txtIdPrograma)
                               ->queryScalar();

                                if ($varVerificar != '0') {
                                    $varMax = Yii::$app->db->createCommand("select max(pregunta1) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varDateBegin and :varDateLast and tbl_base_satisfaccion.pcrc = :txtIdPrograma")
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();


                                    if ($varMax > 5) {
                                        $varContarNSatu = Yii::$app->db->createCommand("select (round(((((select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.pregunta1 in (8,9,10)) - (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.pregunta1 in (0,1,2,3,4,5))) / (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast))*100),2)) as VarNSatu from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on  p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where a.id = :txtIdPrograma and c.id = 1 and a.activo = 0 ")
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarNSatu != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarNSatu,
                                                'nombreencuestas' => 'NSatisfacción',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 3,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        } 

                                    }else{

                                        $varContarSatu = Yii::$app->db->createCommand("select (round(((select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.pregunta1 in (4,5)) / (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 1 and a.activo = 0")
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarSatu != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarSatu,
                                                'nombreencuestas' => 'Satisfacción',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 1,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        }                                

                                        $varContarInSatu = Yii::$app->db->createCommand("select (round(((select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.pregunta1 in (1,2)) / (select count(pregunta1) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 1 and a.activo = 0")
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarInSatu != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarInSatu,
                                                'nombreencuestas' => 'Insatisfacción',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 2,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        }
                                    }

                                }
                            }
                        }
                    }

                }
            }
            return $this->redirect(['index']);
        }

        public function actionParametrizarindicadores4(){
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");

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

            $varServicios = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")->queryAll();

            foreach ($varServicios as $key => $value) {
                $varIdServicios = $value['id'];

                if ($varIdServicios == '118' || $varIdServicios == '17') {
                    $querys =  new Query;
                    $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                ->from('tbl_tipocortes')
                                ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                        'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                ->where('tbl_grupo_cortes.idgrupocorte = 1')
                                ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                        $varIdCorte = $value['idtc'];
                        $varMesYear = $value['mesyear'];
                        $varDateBegin = $varMesYear.' 00:00:00';
                        $varMesYear1 = new DateTime($varMesYear);
                        $varMesYear1->modify('last day of this month');
                        $varDateLast = $varMesYear1->format('Y/m/d').' 23:59:59';

                        $querys1 =  new Query;
                        $querys1    ->select(['tbl_parametrizacion_encuesta.programa'])->distinct()
                                    ->from('tbl_preguntas')
                                    ->join('LEFT OUTER JOIN', 'tbl_parametrizacion_encuesta',
                                            'tbl_preguntas.id_parametrizacion = tbl_parametrizacion_encuesta.id')
                                    ->join('LEFT OUTER JOIN', 'tbl_categorias',
                                            'tbl_preguntas.categoria = tbl_categorias.id')
                                    ->where("tbl_parametrizacion_encuesta.cliente = :varIdServicios")
                                    ->andwhere("tbl_categorias.id = 7")
                                    ->addParams([':varIdServicios'=>$varIdServicios]);                    
                        $command1 = $querys1->createCommand();
                        $query1 = $command1->queryAll();

                        foreach ($query1 as $key => $value) {
                            $txtIdPrograma = $value['programa'];

                            $varVerificar = Yii::$app->db->createCommand("select count(*) from tbl_arbols where id = :txtIdPrograma  and activo = 0")
                            ->bindValue(':txtIdPrograma',$txtIdPrograma)
                            ->queryScalar();

                            if ($varVerificar != '0') {
                                $varPregunta = Yii::$app->db->createCommand("select tbl_preguntas.pre_indicador from tbl_preguntas inner join tbl_parametrizacion_encuesta on tbl_preguntas.id_parametrizacion = tbl_parametrizacion_encuesta.id inner join tbl_categorias on tbl_preguntas.categoria = tbl_categorias.id where tbl_parametrizacion_encuesta.programa = :txtIdPrograma and tbl_categorias.id = 7")
                                ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                ->queryScalar();

                                $varMax = Yii::$app->db->createCommand("select max(:varPregunta) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varDateBegin and :varDateLast and tbl_base_satisfaccion.pcrc = :txtIdPrograma")
                                ->bindValue(':varPregunta',$varPregunta)
                                ->bindValue(':varDateBegin',$varDateBegin)
                                ->bindValue(':varDateLast',$varDateLast)
                                ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                ->queryScalar();

                                if ($varMax > 2) {
                                    $varContarNSolucion = Yii::$app->db->createCommand("select ( round(((select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.:varPregunta in (4,5)) / (select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu, a.name, a.id from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 7 and a.activo = 0")
                                    ->bindValue(':varPregunta',$varPregunta)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();

                                    if ($varContarNSolucion != null) {
                                        Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                            'cantencuestas' => $varContarNSolucion,
                                            'nombreencuestas' => 'NSolución',
                                            'programa_id' => $txtIdPrograma,
                                            'idtc' => $varIdCorte,
                                            'arbol_id' => $varIdServicios,
                                            'mesyear' => $varMesYear,
                                            'indicador' => 4,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladove' => $txtanulado,
                                        ])->execute();
                                    } 
                                }else{
                                    $varContarSolucion = Yii::$app->db->createCommand("select ( round(((select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.:varPregunta in (1)) / (select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu, a.name, a.id from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 7 and a.activo = 0")
                                    ->bindValue(':varPregunta',$varPregunta)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();

                                    if ($varContarSolucion != null) {
                                        Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                            'cantencuestas' => $varContarSolucion,
                                            'nombreencuestas' => 'Solución',
                                            'programa_id' => $txtIdPrograma,
                                            'idtc' => $varIdCorte,
                                            'arbol_id' => $varIdServicios,
                                            'mesyear' => $varMesYear,
                                            'indicador' => 5,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladove' => $txtanulado,
                                        ])->execute();
                                    } 

                                    $varContarNoSolucion = Yii::$app->db->createCommand("select ( round(((select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.:varPregunta in (2)) / (select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu, a.name, a.id from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 7 and a.activo = 0")
                                    ->bindValue(':varPregunta',$varPregunta)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();

                                    if ($varContarNoSolucion != null) {
                                        Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                            'cantencuestas' => $varContarNoSolucion,
                                            'nombreencuestas' => 'NoSolución',
                                            'programa_id' => $txtIdPrograma,
                                            'idtc' => $varIdCorte,
                                            'arbol_id' => $varIdServicios,
                                            'mesyear' => $varMesYear,
                                            'indicador' => 6,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladove' => $txtanulado,
                                        ])->execute();
                                    } 
                                }
                            }
                        }
                    }
                }else{
                    if ($varIdServicios == '237' || $varIdServicios == '1358' || $varIdServicios == '105' || $varIdServicios == '8' || $varIdServicios == '99') {
                        $querys =  new Query;
                        $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 2')
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];
                            $varDateBegin = $varMesYear.' 00:00:00';
                            $varMesYear1 = new DateTime($varMesYear);
                            $varMesYear1->modify('last day of this month');
                            $varDateLast = $varMesYear1->format('Y/m/d').' 23:59:59';

                            $querys1 =  new Query;
                            $querys1    ->select(['tbl_parametrizacion_encuesta.programa'])->distinct()
                                        ->from('tbl_preguntas')
                                        ->join('LEFT OUTER JOIN', 'tbl_parametrizacion_encuesta',
                                                'tbl_preguntas.id_parametrizacion = tbl_parametrizacion_encuesta.id')
                                        ->join('LEFT OUTER JOIN', 'tbl_categorias',
                                                'tbl_preguntas.categoria = tbl_categorias.id')
                                        ->where("tbl_parametrizacion_encuesta.cliente = :varIdServicios")
                                        ->andwhere("tbl_categorias.id = 7")
                                        ->addParams([':varIdServicios'=>$varIdServicios]);                    
                            $command1 = $querys1->createCommand();
                            $query1 = $command1->queryAll();

                            foreach ($query1 as $key => $value) {
                                $txtIdPrograma = $value['programa'];

                                $varVerificar = Yii::$app->db->createCommand("select count(*) from tbl_arbols where id = :txtIdPrograma  and activo = 0")
                                ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                ->queryScalar();

                                if ($varVerificar != '0') {
                                    $varPregunta = Yii::$app->db->createCommand("select tbl_preguntas.pre_indicador from tbl_preguntas inner join tbl_parametrizacion_encuesta on tbl_preguntas.id_parametrizacion = tbl_parametrizacion_encuesta.id inner join tbl_categorias on tbl_preguntas.categoria = tbl_categorias.id where tbl_parametrizacion_encuesta.programa = :txtIdPrograma and tbl_categorias.id = 7")
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();

                                    $varMax = Yii::$app->db->createCommand("select max(:varPregunta) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varDateBegin and :varDateLast and tbl_base_satisfaccion.pcrc = :txtIdPrograma")
                                    ->bindValue(':varPregunta',$varPregunta)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();

                                    if ($varMax > 2) {
                                        $varContarNSolucion = Yii::$app->db->createCommand("select ( round(((select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.:varPregunta in (4,5)) / (select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu, a.name, a.id from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 7 and a.activo = 0")
                                        ->bindValue(':varPregunta',$varPregunta)
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarNSolucion != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarNSolucion,
                                                'nombreencuestas' => 'NSolución',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 4,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        } 
                                    }else{
                                        $varContarSolucion = Yii::$app->db->createCommand("select ( round(((select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.:varPregunta in (1)) / (select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu, a.name, a.id from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 7 and a.activo = 0")
                                        ->bindValue(':varPregunta',$varPregunta)
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarSolucion != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarSolucion,
                                                'nombreencuestas' => 'Solución',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 5,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        } 

                                        $varContarNoSolucion = Yii::$app->db->createCommand("select ( round(((select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.:varPregunta in (2)) / (select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu, a.name, a.id from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 7 and a.activo = 0")
                                        ->bindValue(':varPregunta',$varPregunta)
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarNoSolucion != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarNoSolucion,
                                                'nombreencuestas' => 'NoSolución',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 6,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        }                                         
                                    }
                                }
                            }
                        }
                    }else{
                        $querys =  new Query;
                        $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 3')
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];
                            $varDateBegin = $varMesYear.' 00:00:00';
                            $varMesYear1 = new DateTime($varMesYear);
                            $varMesYear1->modify('last day of this month');
                            $varDateLast = $varMesYear1->format('Y/m/d').' 23:59:59';

                            $querys1 =  new Query;
                            $querys1    ->select(['tbl_parametrizacion_encuesta.programa'])->distinct()
                                        ->from('tbl_preguntas')
                                        ->join('LEFT OUTER JOIN', 'tbl_parametrizacion_encuesta',
                                                'tbl_preguntas.id_parametrizacion = tbl_parametrizacion_encuesta.id')
                                        ->join('LEFT OUTER JOIN', 'tbl_categorias',
                                                'tbl_preguntas.categoria = tbl_categorias.id')
                                        ->where("tbl_parametrizacion_encuesta.cliente = :varIdServicios")
                                        ->andwhere("tbl_categorias.id = 7")
                                        ->addParams([':varIdServicios'=>$varIdServicios]);                    
                            $command1 = $querys1->createCommand();
                            $query1 = $command1->queryAll();

                            foreach ($query1 as $key => $value) {
                                $txtIdPrograma = $value['programa'];

                                $varVerificar = Yii::$app->db->createCommand("select count(*) from tbl_arbols where id = :txtIdPrograma  and activo = 0")
                                ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                ->queryScalar();

                                if ($varVerificar != '0') {
                                    $varPregunta = Yii::$app->db->createCommand("select tbl_preguntas.pre_indicador from tbl_preguntas inner join tbl_parametrizacion_encuesta on tbl_preguntas.id_parametrizacion = tbl_parametrizacion_encuesta.id inner join tbl_categorias on tbl_preguntas.categoria = tbl_categorias.id where tbl_parametrizacion_encuesta.programa = :txtIdPrograma and tbl_categorias.id = 7")
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();

                                    $varMax = Yii::$app->db->createCommand("select max(:varPregunta) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varDateBegin and :varDateLast and tbl_base_satisfaccion.pcrc = :txtIdPrograma")
                                    ->bindValue(':varPregunta',$varPregunta)
                                    ->bindValue(':varDateBegin',$varDateBegin)
                                    ->bindValue(':varDateLast',$varDateLast)
                                    ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                    ->queryScalar();

                                    if ($varMax > 2) {
                                        $varContarNSolucion = Yii::$app->db->createCommand("select ( round(((select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.:varPregunta in (4,5)) / (select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu, a.name, a.id from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 7 and a.activo = 0")
                                        ->bindValue(':varPregunta',$varPregunta)
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarNSolucion != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarNSolucion,
                                                'nombreencuestas' => 'NSolución',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 4,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        } 
                                    }else{
                                        $varContarSolucion = Yii::$app->db->createCommand("select ( round(((select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.:varPregunta in (1)) / (select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu, a.name, a.id from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 7 and a.activo = 0")
                                        ->bindValue(':varPregunta',$varPregunta)
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarSolucion != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarSolucion,
                                                'nombreencuestas' => 'Solución',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 5,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        } 

                                        $varContarNoSolucion = Yii::$app->db->createCommand("select ( round(((select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast and bs.:varPregunta in (2)) / (select count(:varPregunta) from tbl_base_satisfaccion bs where bs.cliente = pe.cliente and bs.pcrc = pe.programa and bs.created between :varDateBegin and :varDateLast) * 100),2) ) as varSatu, a.name, a.id from tbl_preguntas p inner join tbl_parametrizacion_encuesta pe on p.id_parametrizacion = pe.id inner join tbl_categorias c on p.categoria = c.id inner join tbl_arbols a on pe.programa = a.id where pe.programa = :txtIdPrograma and c.id = 7 and a.activo = 0")
                                        ->bindValue(':varPregunta',$varPregunta)
                                        ->bindValue(':varDateBegin',$varDateBegin)
                                        ->bindValue(':varDateLast',$varDateLast)
                                        ->bindValue(':txtIdPrograma',$txtIdPrograma)
                                        ->queryScalar();

                                        if ($varContarNoSolucion != null) {
                                            Yii::$app->db->createCommand()->insert('tbl_voz_encuestas',[
                                                'cantencuestas' => $varContarNoSolucion,
                                                'nombreencuestas' => 'NoSolución',
                                                'programa_id' => $txtIdPrograma,
                                                'idtc' => $varIdCorte,
                                                'arbol_id' => $varIdServicios,
                                                'mesyear' => $varMesYear,
                                                'indicador' => 6,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladove' => $txtanulado,
                                            ])->execute();
                                        } 
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $this->redirect(['index']);
        }


        public function actionEnviarcorreo($nomPCRC){
            $varPcrc = $nomPCRC;

            return $this->renderAjax('enviarcorreo',[
                'varPcrc' => $varPcrc,
                ]);
        }

        public function actionExportar(){

            $varMes = date("n");
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

            $varBeginYear = '2019-01-01';
            $varLastYear = '2030-12-31'; 

            $varPcrc = Yii::$app->request->post("var_Pcrc");
            $varCorreo = Yii::$app->request->post("var_Destino");

            $phpExc = new \PHPExcel();

            $phpExc->getProperties()
                            ->setCreator("Konecta")
                            ->setLastModifiedBy("Konecta")
                            ->setTitle("Escuchar + (Programa VOC - Konecta)")
                            ->setSubject("Escuchar + (Programa VOC - Konecta)")
                            ->setDescription("Este archivo contiene el proceso de las comparaciones con respecto a la valoraciones Manuales y Automaticas. Ademas de esto muestra tambien los valores de la encuestas Satu o InSatu, de los ultimos 6 meses.")
                            ->setKeywords("Escuchar + (Programa VOC - Konecta)");
            $phpExc->setActiveSheetIndex(0);
            $phpExc->getActiveSheet()->setShowGridlines(False);

            $styleArray = array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                );

            $styleArraySize = array(
                    'font' => array(
                        'bold' => true,
                        'size'  => 15,
                    ),
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

            $styleArraySubTitle2 = array(              
              'fill' => array( 
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                        'color' => array('rgb' => 'C6C6C6'),
                    )
            );            

            // TITLE KONECTA EXPERIENCE LEARNING
            $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - QA MANAGEMENT');
            $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
            $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);

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

            

            // STYLE TO ALL DOCUMENT
            $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);


            $phpExc->setActiveSheetIndex(0)->mergeCells('A1:H1');
            $phpExc->getActiveSheet()->SetCellValue('A2','Informe DashBoard Escuchar +');
            $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySize);
            $phpExc->setActiveSheetIndex(0)->mergeCells('A2:H2');

            $phpExc->getActiveSheet()->SetCellValue('A3','METRICAS GENERALES');
            $phpExc->setActiveSheetIndex(0)->mergeCells('A3:H3');
            $phpExc->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);            
            $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArrayTitle);
            $phpExc->getActiveSheet()->SetCellValue('A4','Volúmen de Gestión (Valoraciones Manuales - Automáticas)...');
            $phpExc->setActiveSheetIndex(0)->mergeCells('A4:H4');
            $phpExc->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A4')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A4')->applyFromArray($styleArraySubTitle);
            $phpExc->getActiveSheet()->getStyle('A4')->applyFromArray($styleArrayTitle);

            $numCell = 5;
            $phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'Nivel');
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle2);


            $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between :varBeginYear and :varLastYear group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")
            ->bindValue(':varBeginYear',$varBeginYear)
            ->bindValue(':varLastYear',$varLastYear)
            ->queryAll();
            $lastColumn = 'A';

            foreach ($varMonthYear as $key => $value) {
                $varMonth = $value['CorteMes'];
                $varYear = $value['CorteYear'];

                $lastColumn++;

                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varMonth.' - '.$varYear);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArraySubTitle2);
                
            }
            $numCell = $numCell++ + 1;

            $txtCiudades = Yii::$app->db->createCommand(" select id, name, arbol_id from tbl_arbols where id in (98, 2, 1)")->queryAll(); 

            foreach ($txtCiudades as $key => $value) {
                $txtNameCity = $value['name'];
                $varIdPcrc = $value['id'];
                
                $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $txtNameCity); 
                $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle2);
                
                
                $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between :varBeginYear and :varLastYear group by mesyear order by mesyear desc limit 7) a  order by a.mesyear asc")
                ->bindValue(':varBeginYear',$varBeginYear)
                ->bindValue(':varLastYear',$varLastYear)
                ->queryAll();

                $txtTotalMonth = null;
                $lastColumn = 'A';
                foreach ($varListMonth as $key => $value) {
                    $lastColumn++;
                    $varListYear = $value['mesyear']; 

                    if ($varIdPcrc == 1) {
                        $txtQuery =  new Query;
                        $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                                    ->from('tbl_control_volumenxclientedq')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id in (2, 98)')
                                    ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear]);
                        $command = $txtQuery->createCommand();
                        $txtTotalMonth1 = $command->queryScalar();

                        $txtQuery1 =  new Query;
                        $txtQuery1  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                                    ->from('tbl_control_volumenxclienteds')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id in (2, 98)')
                                    ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear]);
                        $command1 = $txtQuery1->createCommand();
                        $txtTotalMonth2 = $command1->queryScalar(); 

                        $txtTotalMonth = $txtTotalMonth1 + $txtTotalMonth2;
                    }else{
                        $txtQuery =  new Query;
                        $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                                    ->from('tbl_control_volumenxclientedq')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id = :varIdPcrc')
                                    ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear])
                                    ->addParams([':varIdPcrc'=>$varIdPcrc]);
                        $command = $txtQuery->createCommand();
                        $txtTotalMonth1 = $command->queryScalar();                       

                        $txtQuery2 =  new Query;
                        $txtQuery2  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                                    ->from('tbl_control_volumenxclienteds')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id = :varIdPcrc')
                                    ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear])
                                    ->addParams([':varIdPcrc'=>$varIdPcrc]);
                        $command2 = $txtQuery2->createCommand();
                        $txtTotalMonth2 = $command2->queryScalar(); 

                        $txtTotalMonth = $txtTotalMonth1 + $txtTotalMonth2;
                    }

                    $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtTotalMonth);                    
                }   
                $numCell++;
            }
            $numCell = $numCell++ + 1;  

            $phpExc->setActiveSheetIndex(0)->mergeCells('A9:H9');
            $phpExc->getActiveSheet()->SetCellValue('A10','Volúmen de Encuestas...');
            $phpExc->getActiveSheet()->getStyle('A10')->getFont()->setBold(true);
            $phpExc->setActiveSheetIndex(0)->mergeCells('A10:H10');
            $phpExc->getActiveSheet()->getStyle('A10')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A10')->applyFromArray($styleArraySubTitle);
            $phpExc->getActiveSheet()->getStyle('A10')->applyFromArray($styleArrayTitle);

            $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between :varBeginYear and :varLastYear group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")
            ->bindValue(':varBeginYear',$varBeginYear)
            ->bindValue(':varLastYear',$varLastYear)
            ->queryAll();

            $numCell = 11;
            $phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'Nivel');
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle2);

            $lastColumn = 'A';
            foreach ($varMonthYear as $key => $value) {
                $varMonth = $value['CorteMes'];
                $varYear = $value['CorteYear'];

                $lastColumn++;

                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varMonth.' - '.$varYear);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArraySubTitle2);
                
            }
            $numCell = $numCell++ + 1;

            $txtCiudadesE = Yii::$app->db->createCommand(" select id, name, arbol_id from tbl_arbols where id in (98, 2, 1)")->queryAll();            

            foreach ($txtCiudadesE as $key => $value) {
                $txtNameCityE = $value['name'];
                $varIdPcrcE = $value['id'];
                
                $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $txtNameCityE); 
                $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle2);

                $varListMonthE = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between :varBeginYear and :varLastYear group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")
                ->bindValue(':varBeginYear',$varBeginYear)
                ->bindValue(':varLastYear',$varLastYear)
                ->queryAll();

                $lastColumn = 'A';
                foreach ($varListMonthE as $key => $value) {
                    $varListYearE = $value['mesyear']; 
                    $lastColumn++;

                    if ($varIdPcrcE == 1) {
                        $txtQueryE =  new Query;
                        $txtQueryE   ->select(['sum(tbl_control_volumenxencuestasdq.cantidadvalor)'])->distinct()
                                                ->from('tbl_control_volumenxencuestasdq')
                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                            'tbl_control_volumenxencuestasdq.idservicio = tbl_arbols.id')
                                                ->where('tbl_arbols.arbol_id in (2, 98)')
                                                ->andwhere(['between','tbl_control_volumenxencuestasdq.mesyear', $varListYearE, $varListYearE]);                    
                        $commandE = $txtQueryE->createCommand();
                        $txtTotalMonth1E = $commandE->queryScalar();

                        $txtTotalMonthE =  $txtTotalMonth1E;
                    }else{
                        $txtQueryE =  new Query;
                        $txtQueryE   ->select(['sum(tbl_control_volumenxencuestasdq.cantidadvalor)'])->distinct()
                                                ->from('tbl_control_volumenxencuestasdq')
                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                            'tbl_control_volumenxencuestasdq.idservicio = tbl_arbols.id')
                                                ->where('tbl_arbols.arbol_id = :varIdPcrcE')
                                                ->andwhere(['between','tbl_control_volumenxencuestasdq.mesyear', $varListYearE, $varListYearE])
                                                ->addParams([':varIdPcrcE'=>$varIdPcrcE]);                    
                        $commandE = $txtQueryE->createCommand();
                        $txtTotalMonth1E = $commandE->queryScalar(); 

                        $txtTotalMonthE =  $txtTotalMonth1E;
                    }
                    $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtTotalMonthE);
                }

                $numCell++;
            }
            $numCell = $numCell++ + 1; 

            $phpExc->setActiveSheetIndex(0)->mergeCells('A15:H15');
            $phpExc->getActiveSheet()->SetCellValue('A16','INFORMACION DE PARTIDA');
            $phpExc->setActiveSheetIndex(0)->mergeCells('A16:H16');
            $phpExc->getActiveSheet()->getStyle('A16')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A16')->applyFromArray($styleArray);
            $phpExc->getActiveSheet()->getStyle('A16')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A16')->applyFromArray($styleArrayTitle);
            $phpExc->getActiveSheet()->SetCellValue('A17','Ciudad');
            $phpExc->setActiveSheetIndex(0)->mergeCells('A17:D17');
            $phpExc->getActiveSheet()->getStyle('A17')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A17')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A17')->applyFromArray($styleArraySubTitle2);
            $phpExc->getActiveSheet()->SetCellValue('E17','Servicio');
            $phpExc->setActiveSheetIndex(0)->mergeCells('E17:H17');
            $phpExc->getActiveSheet()->getStyle('E17')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('E17')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('E17')->applyFromArray($styleArraySubTitle2);
            $phpExc->getActiveSheet()->SetCellValue('A19','Director');
            $phpExc->setActiveSheetIndex(0)->mergeCells('A19:H19');
            $phpExc->getActiveSheet()->getStyle('A19')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A19')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A19')->applyFromArray($styleArraySubTitle2);
            $phpExc->getActiveSheet()->SetCellValue('A21','Gerente');
            $phpExc->setActiveSheetIndex(0)->mergeCells('A21:H21');
            $phpExc->getActiveSheet()->getStyle('A21')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A21')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A21')->applyFromArray($styleArraySubTitle2);

            $txtGerentes = Yii::$app->db->createCommand("select group_concat(distinct gerentes order by gerentes asc separator ', ') as concatenar from tbl_voz_seleccion vs where arbol_id = :varPcrc and anulado = 0 ")
            ->bindValue(':varPcrc',$varPcrc)
            ->queryScalar();

            $query =  new Query;
            $query      ->select(['tbl_voz_seleccion.ciudad','tbl_procesos_directores.director_programa','tbl_arbols.name'])->distinct()
                                ->from('tbl_voz_seleccion')
                                ->join('LEFT OUTER JOIN', 'tbl_procesos_directores',
                                                'tbl_voz_seleccion.iddirectores = tbl_procesos_directores.iddirectores')
                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                'tbl_voz_seleccion.arbol_id = tbl_arbols.id')
                                ->where(['tbl_arbols.id' => $varPcrc])
                                ->andwhere(['tbl_arbols.activo' => 0])
                                ->andwhere(['tbl_voz_seleccion.anulado' => 0]);
            $command = $query->createCommand();
            $listData = $command->queryAll();  

            $txtCiudad = null;
            $txtDirector = null;
            $txtArbol = null;
            foreach ($listData as $key => $value) {
                $txtCiudad = $value['ciudad'];
                $txtDirector = $value['director_programa'];
                $txtArbol = $value['name'];
            }

            $phpExc->getActiveSheet()->setCellValue('A18', $txtCiudad);
            $phpExc->setActiveSheetIndex(0)->mergeCells('A18:D18');
            $phpExc->getActiveSheet()->setCellValue('E18', $txtArbol);
            $phpExc->setActiveSheetIndex(0)->mergeCells('E18:H18');
            $phpExc->getActiveSheet()->setCellValue('A20', $txtDirector);
            $phpExc->setActiveSheetIndex(0)->mergeCells('A20:H20');
            $phpExc->getActiveSheet()->setCellValue('A22', $txtGerentes);
            $phpExc->setActiveSheetIndex(0)->mergeCells('A22:H22');
            $numCell = $numCell++ + 1;

            $phpExc->setActiveSheetIndex(0)->mergeCells('A23:H23');
            $phpExc->getActiveSheet()->SetCellValue('A24','EXPERIENCIA VOZ DEL CLIENTE');
            $phpExc->setActiveSheetIndex(0)->mergeCells('A24:H24');
            $phpExc->getActiveSheet()->getStyle('A24')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A24')->applyFromArray($styleArray);
            $phpExc->getActiveSheet()->getStyle('A24')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A24')->applyFromArray($styleArrayTitle);

            $phpExc->getActiveSheet()->SetCellValue('A25','Experiencia Emitida: Programa VOC & Valoración Manual -- '.$txtArbol.' --');
            $phpExc->setActiveSheetIndex(0)->mergeCells('A25:H25');
            $phpExc->getActiveSheet()->getStyle('A25')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A25')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A25')->applyFromArray($styleArraySubTitle);
            $phpExc->getActiveSheet()->getStyle('A25')->applyFromArray($styleArrayTitle);

            $varMonthYearA = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between :varBeginYear and :varLastYear group by mesyear order by mesyear desc limit 7) a    order by a.mesyear asc")
            ->bindValue(':varBeginYear',$varBeginYear)
            ->bindValue(':varLastYear',$varLastYear)
            ->queryAll();

            $numCell = 26;
            $phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'Datos Evolutivos');
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle2);
            $lastColumn = 'A';
            foreach ($varMonthYearA as $key => $value) {
                $varMonth = $value['CorteMes'];
                $varYear = $value['CorteYear'];

                $lastColumn++;

                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varMonth.' - '.$varYear);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArraySubTitle2);

            }
            $numCell = $numCell++ + 1;

            $phpExc->getActiveSheet()->setCellValue('A27', 'Automáticas & Manuales');
            $phpExc->getActiveSheet()->getStyle('A27')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A27')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A27')->applyFromArray($styleArraySubTitle2);

            $varControlUnion = Yii::$app->db->createCommand("select sum(sumar) as sumar, mesyear from ((select cantidadvalor as sumar, mesyear from (select sum(cantidadvalor) as cantidadvalor, mesyear from tbl_control_volumenxclienteds where idservicio = :varPcrc and anuladovxcs = 0 group by mesyear desc limit 7) a order by a.mesyear asc) union all (select cantidadvalor as sumar, mesyear from (select sum(cantidadvalor) as cantidadvalor, mesyear from tbl_control_volumenxclientedq where idservicio = :varPcrc and anuladovxc = 0  group by mesyear desc limit 7) a order by a.mesyear asc ) ) unidaTables group by mesyear")
            ->bindValue(':varPcrc',$varPcrc)
            ->queryAll();

            $lastColumn = 'A';
            foreach ($varControlUnion as $key => $value) {
                $txtTotalVM = $value['sumar'];
                $lastColumn++;

                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtTotalVM);
            }
            $numCell = $numCell++ + 1;

            $phpExc->getActiveSheet()->setCellValue('A28', 'Automáticas');
            $phpExc->getActiveSheet()->getStyle('A28')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A28')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A28')->applyFromArray($styleArraySubTitle2);

            $varControlSpeech = Yii::$app->db->createCommand("select cantidadvalor, mesyear from (select sum(cantidadvalor) as cantidadvalor, mesyear from tbl_control_volumenxclienteds where idservicio = :varPcrc and anuladovxcs = 0  group by mesyear desc limit 7) a order by a.mesyear asc")
            ->bindValue(':varPcrc',$varPcrc)
            ->queryAll();

            $lastColumn = 'A';
            foreach ($varControlSpeech as $key => $value) {
                $txtTotalA = $value['cantidadvalor'];
                $lastColumn++;

                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtTotalA);
            }
            $numCell = $numCell++ + 1;

            $phpExc->getActiveSheet()->setCellValue('A29', 'Manuales');
            $phpExc->getActiveSheet()->getStyle('A29')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A29')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A29')->applyFromArray($styleArraySubTitle2);

            $varControlManual = Yii::$app->db->createCommand("select cantidadvalor, mesyear from (select sum(cantidadvalor) as cantidadvalor, mesyear from tbl_control_volumenxclientedq where idservicio = :varPcrc and anuladovxc = 0   group by mesyear desc limit 7) a order by a.mesyear asc")
            ->bindValue(':varPcrc',$varPcrc)
            ->queryAll();

            $lastColumn = 'A';
            foreach ($varControlManual as $key => $value) {
                $txtTotalM = $value['cantidadvalor'];
                $lastColumn++;

                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtTotalM);
            }
            $numCell = $numCell++ + 1;

            $phpExc->setActiveSheetIndex(0)->mergeCells('A30:H30');
            $phpExc->getActiveSheet()->SetCellValue('A31','Experiencia Percibida: Programa Encuestas -- '.$txtArbol.' --');
            $phpExc->setActiveSheetIndex(0)->mergeCells('A31:H31');
            $phpExc->getActiveSheet()->getStyle('A31')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A31:H31')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A31:H31')->applyFromArray($styleArraySubTitle);
            $phpExc->getActiveSheet()->getStyle('A31')->applyFromArray($styleArrayTitle);

            $varMonthYearA = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between :varBeginYear and :varLastYear group by mesyear order by mesyear desc limit 7) a    order by a.mesyear asc")
            ->bindValue(':varBeginYear',$varBeginYear)
            ->bindValue(':varLastYear',$varLastYear)
            ->queryAll();

            $numCell = 32;
            $phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'Datos Evolutivos');
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle2);
            $lastColumn = 'A';
            foreach ($varMonthYearA as $key => $value) {
                $varMonth = $value['CorteMes'];
                $varYear = $value['CorteYear'];
                $lastColumn++;

                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varMonth.' - '.$varYear);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArraySubTitle2);

            }
            $numCell = $numCell++ + 1;

            $phpExc->getActiveSheet()->setCellValue('A33', 'Cantidad Encuestas');
            $phpExc->getActiveSheet()->getStyle('A33')->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle('A33')->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle('A33')->applyFromArray($styleArraySubTitle2);

            $varControlEncuestas = Yii::$app->db->createCommand("select totalvalor, mesyear from (select sum(cantidadvalor)as totalvalor, mesyear from tbl_control_volumenxencuestasdq where idservicio = :varPcrc and anuladovxedq = 0 group by mesyear desc limit 7) a order by a.mesyear asc")
            ->bindValue(':varPcrc',$varPcrc)
            ->queryAll(); 

            $lastColumn = 'A';
            foreach ($varControlEncuestas as $key => $value) {
                $txtTotalEncuestas = $value['totalvalor'];
                $lastColumn++;

                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtTotalEncuestas);
            }
            $numCell = $numCell++ + 1;


            $hoy = getdate();
            $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_DashBoard_Escuchar+(ProgramaVoc)";
              
            $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                
            $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
            $tmpFile.= ".xls";

            $objWriter->save($tmpFile);

            $message = "<html><body>";
            $message .= "<h3>Se ha realizado el envio correcto del archivo Escuchar + Programa VOC.</h3>";
            $message .= "</body></html>";

            Yii::$app->mailer->compose()
                        ->setTo($varCorreo)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject("Envio Dashboard Escuchar + (Programa VOC - Konecta)")
                        ->attach($tmpFile)
                        ->setHtmlBody($message)
                        ->send();

            $rtaenvio = 1;
            die(json_encode($rtaenvio));
        }

        public function actionGraficamanuales(){
            $model = new ControlVolumenxclientedq;

            $varServicio = null;
            $varMesyear = null;

            $data = Yii::$app->request->post();
            if ($model->load($data)){
                $varServicio = $model->idservicio;
                $varMesyear = $model->mesyear;
            }

            return $this->render('graficamanuales',[
                'model' => $model,
                'varServicio' => $varServicio,
                'varMesyear' => $varMesyear,
                ]);
        }

        public function actionGraficaencuestas(){
            return $this->renderAjax('graficaencuestas');
        }

        public function actionGraficavaloracion(){
            return $this->renderAjax('graficavaloracion');
        }

        public function actionGraficaindividual($varServicio){
            $model = new ControlVolumenxclientedq;
            $varServicio = $varServicio;
            $varMesyear = null;

            $data = Yii::$app->request->post();
            if ($model->load($data)){
                $varMesyear = $model->mesyear;
            }

            return $this->render('graficaindividual',[
                'model' => $model,
                'varServicio' => $varServicio,
                'varMesyear' => $varMesyear,
                ]);
        }

        public function actionGraficaencuestasday(){
            $model = new ControlVolumenxencuestasdq;
            $varServicio = null;
            $varMesyear = null;

            $data = Yii::$app->request->post();
            if ($model->load($data)){
                $varServicio = $model->idservicio;
                $varMesyear = $model->mesyear;
            }

            return $this->render('graficaencuestasday',[
                'model' => $model,
                'varServicio' => $varServicio,
                'varMesyear' => $varMesyear,
                ]);
        }

        public function actionVocvideo($varvideo){
            $vartvideo = $varvideo;

            return $this->renderAjax('vocvideo',[
                'vartvideo' => $vartvideo,
                ]);
        }


    }

?>
