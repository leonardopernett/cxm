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
use app\models\Controlvolumenxvalorador;

    class DashboardcontrolController extends \yii\web\Controller {

        public function behaviors(){
            return[
                'access' => [
                        'class' => AccessControl::classname(),
                        'only' => ['index','tablerocontrol','vervolumengestion','estructurarcliente', 'parametrizardatos', 'parametrizardaos2', 'vervolumenvalorador','parametrizardatos3','vervolumencostos','parametrizardatos4','medellinvolumencliente','bogotavolumencliente', 'datosmetricas','parametrizardatos5','vertotalidadqa','vertotalidadsp','vertotalidadgeneral','verclienteqa','verclientesp','datosformularios','datosformularios1','parametrizardatosdayqa','parametrizarencuestasdq','parametrizardatosdaysp'],
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
        
        public function actionIndex(){
            return $this->render('index');
        }

        public function actionTablerocontrol(){


            return $this->renderAjax('tablerocontrol');
        }

        public function actionEstructurarcliente(){

            return $this->renderAjax('estructurarcliente');
        }

        public function actionVervolumengestion(){

            return $this->render('vervolumengestion');
        }

        public function actionVervolumenvalorador(){
                    $model = new Controlvolumenxvalorador();
                    $varIdServicio = 0;

                    $data = Yii::$app->request->post();
                    $model->load($data);

                    if ($model->load($data)) {
                        $varIdServicio = $model->idservicio;
                    } 

                    return $this->render('vervolumenvalorador',[
                        'model' => $model,
                        'varIdServicio' => $varIdServicio,
                    ]);
        }

            public function actionVervolumencostos(){

                    return $this->render('vervolumencostos');
            }     

        public function actionParametrizardatos(){

            return $this->renderAjax('parametrizardatos');  
        }

        public function actionParametrizardatos2(){
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
                                ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', ':txtMes'])
                                ->addParams([':txtMes' => $txtMes]);                    
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                        $varIdCorte = $value['idtc'];
                        $varDateBegin = $value['fechainiciotc'];
                        $varDateLast = $value['fechafintc'];
                        $varMesYear = $value['mesyear'];

                        $querys =  new Query;
                        $querys     ->select(['tbl_ejecucionformularios.created','tbl_usuarios.usua_id'])->distinct()
                                    ->from('tbl_ejecucionformularios')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id')
                                    ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                                'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                    ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                                'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                    ->join('LEFT OUTER JOIN', 'tbl_roles',
                                                'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                    ->where(['between','tbl_ejecucionformularios.created', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                    ->andwhere('tbl_arbols.arbol_id = :varIdServicios')
                                    ->andwhere(['in','tbl_dimensions.id',[1, 11]])
                                    ->andwhere(['in','tbl_roles.role_id',[272, 291]])
                                    ->addParams([':varDateBegin' => $varDateBegin])
                                    ->addParams([':varDateLast' => $varDateLast])
                                    ->addParams([':varIdServicios' => $varIdServicios]);
                                    
                        $command = $querys->createCommand();
                        $queryss = $command->queryAll(); 

                        $query = count($queryss);  

                        $varService = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxcliente where idservicio = :varIdServicios and idtc = :varIdCorte and anuladovxc = 0')
                        ->addParams([':varIdServicios' => $varIdServicios])
                        ->addParams([':varIdCorte' => $varIdCorte])
                        ->queryScalar();

                        if ($varService == 0) {
                            Yii::$app->db->createCommand()->insert('tbl_control_volumenxcliente',[
                                    'idservicio' => $varIdServicios,
                                    'idtc' => $varIdCorte,
                                    'mesyear' => $varMesYear,
                                    'cantidadvalor' => $query,
                                    'fechacreacion' => $txtfechacreacion,
                                    'anuladovxc' => $txtanulado,
                                ])->execute();  
                        }

                            $varFormularios2 = Yii::$app->db->createCommand('select id from tbl_arbols where arbol_id = :varIdServicios and activo = 0')
                            ->addParams([':varIdServicios' => $varIdServicios])
                            ->queryAll();

                            foreach ($varFormularios2 as $key => $value) {
                                $txtIdPcrc = $value['id'];

                            $querys =  new Query;
                            $querys     ->select(['tbl_ejecucionformularios.created','tbl_usuarios.usua_id'])->distinct()
                                        ->from('tbl_ejecucionformularios')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                                'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                                'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_roles',
                                                'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                        ->where(['between','tbl_ejecucionformularios.created', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                        ->andwhere('tbl_arbols.id = '.$txtIdPcrc.'')
                                        ->andwhere(['in','tbl_dimensions.id',[1, 11]])
                                        ->andwhere(['in','tbl_roles.role_id',[272, 291]])
                                        ->addParams([':varDateBegin' => $varDateBegin])
                                        ->addParams([':varDateLast' => $varDateLast]);
                                            
                                $command = $querys->createCommand();
                                $queryss = $command->queryAll(); 

                                $query = count($queryss);   

                                $varService1 = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxformulario where arbol_id = :txtIdPcrc and idtc = :varIdCorte and anuladovxf = 0')
                                ->bindValue(':txtIdPcrc', $txtIdPcrc)
                                ->bindValue(':varIdCorte', $varIdCorte)
                                ->queryScalar();

                                if ($varService1 == 0) {
                                    Yii::$app->db->createCommand()->insert('tbl_control_volumenxformulario',[
                                            'idservicio' => $varIdServicios,
                                            'arbol_id' => $txtIdPcrc,
                                            'totalrealizadas' => $query,
                                            'idtc' => $varIdCorte,
                                            'mesyear' => $varMesYear,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladovxf' => $txtanulado,
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
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', ':txtMes'])
                                    ->addParams([':txtMes' => $txtMes]);
                        $command = $querys->createCommand();
                        $query = $command->queryAll();
                       

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varDateBegin = $value['fechainiciotc'];
                            $varDateLast = $value['fechafintc'];
                        $varMesYear = $value['mesyear'];

                            $querys =  new Query;
                            $querys     ->select(['tbl_ejecucionformularios.created','tbl_usuarios.usua_id'])->distinct()
                                        ->from('tbl_ejecucionformularios')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                    'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                                    'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                                    'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_roles',
                                                    'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                        ->where(['between','tbl_ejecucionformularios.created', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                        ->andwhere('tbl_arbols.arbol_id = :varIdServicios')
                                        ->andwhere(['in','tbl_dimensions.id',[1, 11, 2]])
                                        ->andwhere(['in','tbl_roles.role_id',[272, 291]])
                                        ->addParams([':varDateBegin' => $varDateBegin])
                                        ->addParams([':varDateLast' => $varDateLast])
                                        ->addParams([':varIdServicios' => $varIdServicios]);
                                        
                            $command = $querys->createCommand();
                            $queryss = $command->queryAll(); 

                            $query = count($queryss);  

                            $varService = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxcliente where idservicio = :varIdServicios and idtc = :varIdCorte and anuladovxc = 0')
                            ->bindValue(':varIdServicios', $varIdServicios)
                            ->bindValue(':varIdCorte', $varIdCorte)
                            ->queryScalar();

                            if ($varService == 0) {
                                Yii::$app->db->createCommand()->insert('tbl_control_volumenxcliente',[
                                        'idservicio' => $varIdServicios,
                                        'idtc' => $varIdCorte,
                                        'mesyear' => $varMesYear,
                                        'cantidadvalor' => $query,
                                        'fechacreacion' => $txtfechacreacion,
                                        'anuladovxc' => $txtanulado,
                                    ])->execute();  
                            }

                            $varFormularios2 = Yii::$app->db->createCommand('select id from tbl_arbols where arbol_id = :varIdServicios and activo = 0')
                            ->bindValue(':varIdServicios', $varIdServicios)
                            ->queryAll();

                            foreach ($varFormularios2 as $key => $value) {
                                $txtIdPcrc = $value['id'];

                                $querys =  new Query;
                                $querys     ->select(['tbl_ejecucionformularios.created','tbl_usuarios.usua_id'])->distinct()
                                            ->from('tbl_ejecucionformularios')
                                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                    'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id')
                                            ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                                    'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                            ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                                    'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                            ->join('LEFT OUTER JOIN', 'tbl_roles',
                                                    'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                            ->where(['between','tbl_ejecucionformularios.created', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                            ->andwhere('tbl_arbols.id = :txtIdPcrc')
                                            ->andwhere(['in','tbl_dimensions.id',[1, 11, 2]])
                                            ->andwhere(['in','tbl_roles.role_id',[272, 291]])
                                            ->addParams([':varDateBegin' => $varDateBegin])
                                            ->addParams([':varDateLast' => $varDateLast])
                                            ->addParams([':txtIdPcrc' => $txtIdPcrc]);
                                            
                                $command = $querys->createCommand();
                                $queryss = $command->queryAll(); 

                                $query = count($queryss);   

                                $varService1 = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxformulario where arbol_id = :txtIdPcrc and idtc = :varIdCorte and anuladovxf = 0')
                                ->bindValue(':txtIdPcrc', $txtIdPcrc)
                                ->bindValue(':varIdCorte', $varIdCorte)
                                ->queryScalar();

                                if ($varService1 == 0) {
                                    Yii::$app->db->createCommand()->insert('tbl_control_volumenxformulario',[
                                            'idservicio' => $varIdServicios,
                                            'arbol_id' => $txtIdPcrc,
                                            'totalrealizadas' => $query,
                                            'idtc' => $varIdCorte,
                                            'mesyear' => $varMesYear,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladovxf' => $txtanulado,
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
                                    ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', ':txtMes'])
                                    ->addParams([':txtMes' => $txtMes]);
                        $command = $querys->createCommand();
                        $query = $command->queryAll();
                       

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varDateBegin = $value['fechainiciotc'];
                            $varDateLast = $value['fechafintc'];
                        $varMesYear = $value['mesyear'];

                            $querys =  new Query;
                            $querys     ->select(['tbl_ejecucionformularios.created','tbl_usuarios.usua_id'])->distinct()
                                        ->from('tbl_ejecucionformularios')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                        'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                                        'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                                        'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_roles',
                                                        'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                        ->where(['between','tbl_ejecucionformularios.created', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                        ->andwhere('tbl_arbols.arbol_id = :varIdServicios')
                                        ->andwhere(['in','tbl_dimensions.id',[1, 11]])
                                        ->andwhere(['in','tbl_roles.role_id',[272, 291]])
                                        ->addParams([':varDateBegin' => $varDateBegin])
                                        ->addParams([':varDateLast' => $varDateLast])
                                        ->addParams([':varIdServicios' => $varIdServicios]);
                                        
                            $command = $querys->createCommand();
                            $queryss = $command->queryAll(); 

                            $query = count($queryss);  

                            $varService = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxcliente where idservicio = :varIdServicios and idtc = :varIdCorte and anuladovxc = 0')
                            ->bindValue(':varIdServicios', $varIdServicios)
                            ->bindValue(':varIdCorte', $varIdCorte)
                            ->queryScalar();

                            if ($varService == 0) {
                                Yii::$app->db->createCommand()->insert('tbl_control_volumenxcliente',[
                                        'idservicio' => $varIdServicios,
                                        'idtc' => $varIdCorte,
                                    'mesyear' => $varMesYear,
                                        'cantidadvalor' => $query,
                                        'fechacreacion' => $txtfechacreacion,
                                        'anuladovxc' => $txtanulado,
                                    ])->execute();  
                            } 

                            $varFormularios2 = Yii::$app->db->createCommand('select id from tbl_arbols where arbol_id = :varIdServicios and activo = 0')
                            ->bindValue(':varIdServicios', $varIdServicios)
                            ->queryAll();

                            foreach ($varFormularios2 as $key => $value) {
                                $txtIdPcrc = $value['id'];

                                $querys =  new Query;
                                $querys     ->select(['tbl_ejecucionformularios.created','tbl_usuarios.usua_id'])->distinct()
                                            ->from('tbl_ejecucionformularios')
                                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                        'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id')
                                            ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                                        'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                            ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                                        'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                            ->join('LEFT OUTER JOIN', 'tbl_roles',
                                                        'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                            ->where(['between','tbl_ejecucionformularios.created', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                            ->andwhere('tbl_arbols.id = :txtIdPcrc')
                                            ->andwhere(['in','tbl_dimensions.id',[1, 11]])
                                            ->andwhere(['in','tbl_roles.role_id',[272, 291]])
                                            ->addParams([':varDateBegin' => $varDateBegin])
                                            ->addParams([':varDateLast' => $varDateLast])
                                            ->addParams([':txtIdPcrc' => $txtIdPcrc]);
                                            
                                $command = $querys->createCommand();
                                $queryss = $command->queryAll(); 

                                $query = count($queryss);   

                                $varService1 = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxformulario where arbol_id = :txtIdPcrc and idtc = :varIdCorte and anuladovxf = 0')
                                ->bindValue(':txtIdPcrc', $txtIdPcrc)
                                ->bindValue(':varIdCorte', $varIdCorte)
                                ->queryScalar();

                                if ($varService1 == 0) {
                                    Yii::$app->db->createCommand()->insert('tbl_control_volumenxformulario',[
                                            'idservicio' => $varIdServicios,
                                            'arbol_id' => $txtIdPcrc,
                                            'totalrealizadas' => $query,
                                            'idtc' => $varIdCorte,
                                            'mesyear' => $varMesYear,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladovxf' => $txtanulado,
                                        ])->execute();  
                                }                            
                            }
                          
                        }                       
                    }
                }
            }

                    return $this->redirect(['index']);
        }

        public function actionParametrizardatos3(){
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");

            $querys =  new Query;
            $querys     ->select(['tbl_control_volumenxcliente.idservicio as ServicioID', 'tbl_control_volumenxcliente.idtc as CorteID', 'tbl_tipocortes.fechainiciotc as FechaInicio', 'tbl_tipocortes.fechafintc as FechaFin', 'tbl_control_volumenxcliente.mesyear'])
                        ->from('tbl_control_volumenxcliente')
                        ->join('LEFT OUTER JOIN', 'tbl_tipocortes',
                                'tbl_control_volumenxcliente.idtc = tbl_tipocortes.idtc');                    
            $command = $querys->createCommand();
            $query = $command->queryAll();

            foreach ($query as $key => $value) {
                $varServicio = $value['ServicioID'];
                $varFechaInicio = $value['FechaInicio'];
                $varFechaFin = $value['FechaFin'];
                $varMesYear = $value['mesyear'];
                $varCorteId = $value['CorteID'];


                $varService = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxvalorador where idservicio = :varServicio and mesyear = :varMesYear and anuladovxv = 0')
                ->bindValue(':varServicio', $varServicio)
                ->bindValue(':varMesYear', $varMesYear)
                ->queryScalar();

                if ($varServicio == '17' || $varServicio == '118' ) {                    

                    if ($varService == 0) {
                        $querys2 =  new Query;
                        $querys2     ->select(['count(tbl_ejecucionformularios.created) as Realizadas', 'tbl_usuarios.usua_id as UsuarioId', 'tbl_usuarios.usua_identificacion as Identificacion', 'tbl_usuarios.usua_nombre as Nombres'])->distinct()
                                    ->from('tbl_ejecucionformularios')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                            'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
                                    ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                            'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                    ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                            'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                    ->join('LEFT OUTER JOIN', 'tbl_roles',
                                            'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                    ->where(['between','tbl_ejecucionformularios.created', ':varFechaInicio 00:00:00', ':varFechaFin 23:59:59'])
                                    ->andwhere('tbl_arbols.arbol_id = :varServicio')
                                    ->andwhere(['in','tbl_roles.role_id',[272, 291]])
                                    ->andwhere(['in','tbl_dimensions.id',[1, 11]])
                                    ->addParams([':varFechaInicio' => $varFechaInicio])
                                    ->addParams([':varFechaFin' => $varFechaFin])
                                    ->addParams([':varServicio' => $varServicio])
                                    ->groupby('tbl_usuarios.usua_id');                    
                        $command2 = $querys2->createCommand();
                        $query2 = $command2->queryAll();

                        foreach ($query2 as $key => $value) {
                            $varRealizadas = $value['Realizadas'];
                            $varUsuarioId = $value['UsuarioId'];
                            $varIdentificacion = $value['Identificacion'];
                            $varNombres = $value['Nombres'];

                            Yii::$app->db->createCommand()->insert('tbl_control_volumenxvalorador',[
                                            'idservicio' => $varServicio,
                                            'totalrealizadas' => $varRealizadas,
                                            'usua_id' => $varUsuarioId,
                                            'identificacion' => $varIdentificacion,
                                            'nombres' => $varNombres,
                                            'idtc' => $varCorteId,
                                            'mesyear' => $varMesYear,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladovxv' => $txtanulado,
                                        ])->execute(); 

                        }
                    }
                }else{
                    if ($varServicio == '237' || $varServicio == '1358' || $varServicio == '105' || $varServicio == '8' || $varServicio == '99') {
                        if ($varService == 0) {
                            $querys2 =  new Query;
                            $querys2     ->select(['count(tbl_ejecucionformularios.created) as Realizadas', 'tbl_usuarios.usua_id as UsuarioId', 'tbl_usuarios.usua_identificacion as Identificacion', 'tbl_usuarios.usua_nombre as Nombres'])->distinct()
                                        ->from('tbl_ejecucionformularios')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                                'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                                'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_roles',
                                                'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                        ->where(['between','tbl_ejecucionformularios.created', ':varFechaInicio 00:00:00', ':varFechaFin 23:59:59'])
                                        ->andwhere('tbl_arbols.arbol_id = :varServicio')
                                        ->andwhere(['in','tbl_roles.role_id',[272, 291]])
                                        ->andwhere(['in','tbl_dimensions.id',[1, 11, 2]])
                                        ->addParams([':varFechaInicio' => $varFechaInicio])
                                        ->addParams([':varFechaFin' => $varFechaFin])
                                        ->addParams([':varServicio' => $varServicio])
                                        ->groupby('tbl_usuarios.usua_id');
                            $command2 = $querys2->createCommand();
                            $query2 = $command2->queryAll();

                            foreach ($query2 as $key => $value) {
                                $varRealizadas = $value['Realizadas'];
                                $varUsuarioId = $value['UsuarioId'];
                                $varIdentificacion = $value['Identificacion'];
                                $varNombres = $value['Nombres'];

                                Yii::$app->db->createCommand()->insert('tbl_control_volumenxvalorador',[
                                                'idservicio' => $varServicio,
                                                'totalrealizadas' => $varRealizadas,
                                                'usua_id' => $varUsuarioId,
                                                'identificacion' => $varIdentificacion,
                                                'nombres' => $varNombres,
                                                'idtc' => $varCorteId,
                                                'mesyear' => $varMesYear,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladovxv' => $txtanulado,
                                            ])->execute(); 

                            }
                        }                        
                    }else{
                        if ($varService == 0) {
                            $querys2 =  new Query;
                            $querys2     ->select(['count(tbl_ejecucionformularios.created) as Realizadas', 'tbl_usuarios.usua_id as UsuarioId', 'tbl_usuarios.usua_identificacion as Identificacion', 'tbl_usuarios.usua_nombre as Nombres'])->distinct()
                                        ->from('tbl_ejecucionformularios')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                                'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                                'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_roles',
                                                'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                        ->where(['between','tbl_ejecucionformularios.created', ':varFechaInicio 00:00:00', ':varFechaFin 23:59:59'])
                                        ->andwhere('tbl_arbols.arbol_id = :varServicio')
                                        ->andwhere(['in','tbl_roles.role_id',[272, 291]])
                                        ->andwhere(['in','tbl_dimensions.id',[1, 11]])
                                        ->addParams([':varFechaInicio' => $varFechaInicio])
                                        ->addParams([':varFechaFin' => $varFechaFin])
                                        ->addParams([':varServicio' => $varServicio])
                                        ->groupby('tbl_usuarios.usua_id');                    
                            $command2 = $querys2->createCommand();
                            $query2 = $command2->queryAll();

                            foreach ($query2 as $key => $value) {
                                $varRealizadas = $value['Realizadas'];
                                $varUsuarioId = $value['UsuarioId'];
                                $varIdentificacion = $value['Identificacion'];
                                $varNombres = $value['Nombres'];

                                Yii::$app->db->createCommand()->insert('tbl_control_volumenxvalorador',[
                                                'idservicio' => $varServicio,
                                                'totalrealizadas' => $varRealizadas,
                                                'usua_id' => $varUsuarioId,
                                                'identificacion' => $varIdentificacion,
                                                'nombres' => $varNombres,
                                                'idtc' => $varCorteId,
                                                'mesyear' => $varMesYear,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladovxv' => $txtanulado,
                                            ])->execute(); 

                            }
                        }                        
                    }
                }

            }       return $this->redirect(['index']);
        }

        public function actionParametrizardatos4(){
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");

            $txtClientes = Yii::$app->db->createCommand("select * from tbl_control_volumenxcliente where anuladovxc = 0")->queryAll();

            foreach ($txtClientes as $key => $value) {
                $varIdservicios = $value['idservicio'];
                $varIdcorte = $value['idtc'];
                $varMesYear = $value['mesyear'];

                $txtValoradores = Yii::$app->db->createCommand('select * from tbl_control_volumenxvalorador where  idservicio = :varIdservicios and idtc = :varIdcorte and anuladovxv = 0')
                ->bindValue(':varIdservicios', $varIdservicios)
                ->bindValue(':varIdcorte', $varIdcorte)
                ->queryAll();

                foreach ($txtValoradores as $key => $value) {
                    $varIdentificacion = $value['identificacion'];
                    $varNombres = $value['nombres'];

                    $txtSalarioConCarga = Yii::$app->get('dbTeo')->createCommand("select round((sum(importe) * 1.3833)) as SalarioConCargas from teo.teo_nomina_efectiva where documento = :varIdentificacion and fecha_Periodo between :varMesYear AND last_day(:varMesYear) and (codConcepNomina = 100 || codConcepNomina  = 1000)")
                    ->bindValue(':varIdentificacion', $varIdentificacion)
                    ->bindValue(':varMesYear', $varMesYear)
                    ->queryScalar();

                    Yii::$app->db->createCommand()->insert('tbl_control_volumenxcostos',[
                                        'idservicio' => $varIdservicios,
                                        'idtc' => $varIdcorte,
                                        'mesyear' => $varMesYear,
                                        'nombres' => $varNombres,
                                        'salarioconcarga' => $txtSalarioConCarga,
                                        'fechacreacion' => $txtfechacreacion,
                                        'anuladovxv' => $txtanulado,
                                    ])->execute(); 

                }


            }

            return $this->redirect(['index']);
        }

        public function actionMedellinvolumencliente(){

            return $this->renderAjax('medellinvolumencliente');
        }

        public function actionBogotavolumencliente(){

            return $this->renderAjax('bogotavolumencliente');
        } 

        public function actionDatosmetricas(){

            return $this->renderAjax('datosmetricas');
        } 


        public function actionParametrizardatos5(){
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");
            $txtCategoria = 2681;
            $txtCategoria1 = 1105;
            $txtCategoria2 = 1114;

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

            $querys =  new Query;
            $querys     ->select(['tbl_dashboardservicios.arbol_id','tbl_dashboardservicios.clientecategoria'])->distinct()
                        ->from('tbl_dashboardservicios');                   
            $command = $querys->createCommand();
            $query = $command->queryAll();        

            foreach ($query as $key => $value) {
                $txtArbolId = $value['arbol_id'];

                if ($txtArbolId == '118' || $txtArbolId == '17') {
                    $querys1 =  new Query;
                    $querys1    ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                ->from('tbl_tipocortes')
                                ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                                    'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                ->where('tbl_grupo_cortes.idgrupocorte = 1');
                                //->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                    $command1 = $querys1->createCommand();
                    $query1 = $command1->queryAll(); 

                    foreach ($query1 as $key => $value) {
                        $varIdCorte = $value['idtc'];
                        $varMesYear = $value['mesyear'];

                        $varDateBegin1 = $varMesYear;
                        $varDateBegin = $varDateBegin1.' 05:00:00';

                        $varDateLast1 = date('Y-m-d',strtotime($varMesYear."+ 1 month"));
                        $varDateLast = $varDateLast1.' 05:00:00';

                        $txtListService = Yii::$app->db->createCommand('select * from tbl_dashboardservicios where arbol_id = :txtArbolId and anulado = 0')
                        ->bindValue(':txtArbolId', $txtArbolId)
                        ->queryAll();

                        $txtSumCount = 0;
                        foreach ($txtListService as $key => $value) {
                            $vartxtService = $value['clientecategoria'];

                            if ($varMesYear < '2020-01-01') {
                                $querys11 = new Query;
                                $querys11   ->select(['tbl_dashboardspeechcalls.idcategoria'])
                                            ->from('tbl_dashboardspeechcalls')
                                            ->where(['like','tbl_dashboardspeechcalls.servicio',$vartxtService])
                                            ->andwhere('tbl_dashboardspeechcalls.idcategoria ='.$txtCategoria.'')
                                            ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                            ->andwhere('tbl_dashboardspeechcalls.anulado = :txtanulado')
                                            ->addParams([':varDateBegin' => $varDateBegin])
                                            ->addParams([':varDateLast' => $varDateLast])
                                            ->addParams([':txtanulado' => $txtanulado]);
                                $command11 = $querys11->createCommand();
                                $query11 = $command11->queryAll();  

                                $txtvarCount11 = count($query11); 

                                $txtSumCount = $txtSumCount + $txtvarCount11; 
                            }else{
                                $querys11 = new Query;
                                $querys11   ->select(['tbl_dashboardspeechcalls.idcategoria'])
                                            ->from('tbl_dashboardspeechcalls')
                                            ->where(['like','tbl_dashboardspeechcalls.servicio',':vartxtService'])
                                            ->andwhere('tbl_dashboardspeechcalls.idcategoria = :txtCategoria1')
                                            ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                            ->andwhere('tbl_dashboardspeechcalls.anulado = :txtanulado')
                                            ->addParams([':vartxtService' => $vartxtService])
                                            ->addParams([':txtCategoria1' => $txtCategoria1])
                                            ->addParams([':varDateBegin' => $varDateBegin])
                                            ->addParams([':varDateLast' => $varDateLast])
                                            ->addParams([':txtanulado' => $txtanulado]);
                                $command11 = $querys11->createCommand();
                                $query11 = $command11->queryAll();  

                                $txtvarCount11 = count($query11); 

                                $txtSumCount = $txtSumCount + $txtvarCount11;
                            }                      
                        }

                        $verContarList = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxclienteS where idservicio = :txtArbolId and idtc = :varIdCorte and anuladovxcS = 0')
                        ->bindValue(':txtArbolId', $txtArbolId)
                        ->bindValue(':varIdCorte', $varIdCorte)
                        ->queryScalar();

                        if ($verContarList == 0) {
                            Yii::$app->db->createCommand()->insert('tbl_control_volumenxclienteS',[
                                'idservicio' => $txtArbolId,
                                'idtc' => $varIdCorte,
                                'mesyear' => $varMesYear,
                                'cantidadvalorS' => $txtSumCount,
                                'fechacreacion' => $txtfechacreacion,
                                'anuladovxcS' => $txtanulado,
                            ])->execute();                                        
                        }
                    }                    
                }else{
                    if ($txtArbolId == '237' || $txtArbolId == '1358' || $txtArbolId == '105' || $txtArbolId == '8' || $txtArbolId == '99') {
                        $querys2 =  new Query;
                        $querys2    ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                                        'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 2');
                                    //->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command2 = $querys2->createCommand();
                        $query2 = $command2->queryAll();  

                        foreach ($query2 as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];

                            $varDateBegin1 = $varMesYear;
                            $varDateBegin = $varDateBegin1.' 05:00:00';

                            $varDateLast1 = date('Y-m-d',strtotime($varMesYear."+ 1 month"));
                            $varDateLast = $varDateLast1.' 05:00:00';

                            $txtListService = Yii::$app->db->createCommand('select * from tbl_dashboardservicios where arbol_id = :txtArbolId and anulado = 0')
                            ->bindValue(':txtArbolId', $txtArbolId)
                            ->queryAll();

                            $txtSumCount = 0;
                            foreach ($txtListService as $key => $value) {
                                $vartxtService = $value['clientecategoria'];

                                if ($varMesYear < '2020-01-01') {
                                    $querys22 = new Query;
                                    $querys22   ->select(['tbl_dashboardspeechcalls.idcategoria'])
                                                ->from('tbl_dashboardspeechcalls')
                                                ->where(['like','tbl_dashboardspeechcalls.servicio', ':vartxtService'])
                                                ->andwhere('tbl_dashboardspeechcalls.idcategoria = :txtCategoria')
                                                ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                                ->andwhere('tbl_dashboardspeechcalls.anulado = :txtanulado')
                                                ->addParams([':vartxtService' => $vartxtService])
                                                ->addParams([':txtCategoria' => $txtCategoria])
                                                ->addParams([':varDateBegin' => $varDateBegin])
                                                ->addParams([':varDateLast' => $varDateLast])
                                                ->addParams([':txtanulado' => $txtanulado]);
                                    $command22 = $querys22->createCommand();
                                    $query22 = $command22->queryAll();  

                                    $txtvarCount22 = count($query22); 

                                    $txtSumCount = $txtSumCount + $txtvarCount22;
                                }else{
                                    $querys22 = new Query;
                                    $querys22   ->select(['tbl_dashboardspeechcalls.idcategoria'])
                                                ->from('tbl_dashboardspeechcalls')
                                                ->where(['like','tbl_dashboardspeechcalls.servicio',':vartxtService'])
                                                ->andwhere('tbl_dashboardspeechcalls.idcategoria = :txtCategoria1')
                                                ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                                ->andwhere('tbl_dashboardspeechcalls.anulado = :txtanulado')
                                                ->addParams([':vartxtService' => $vartxtService])
                                                ->addParams([':txtCategoria1' => $txtCategoria1])
                                                ->addParams([':varDateBegin' => $varDateBegin])
                                                ->addParams([':varDateLast' => $varDateLast])
                                                ->addParams([':txtanulado' => $txtanulado]);
                                    $command22 = $querys22->createCommand();
                                    $query22 = $command22->queryAll();  

                                    $txtvarCount22 = count($query22); 

                                    $txtSumCount = $txtSumCount + $txtvarCount22;
                                }                               
                            }

                            $verContarList2 = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxclienteS where idservicio = :txtArbolId and idtc = :varIdCorte and anuladovxcS = 0')
                            ->bindValue(':txtArbolId', $txtArbolId)
                            ->bindValue(':varIdCorte', $varIdCorte)
                            ->queryScalar();

                            if ($verContarList2 == 0) {
                                Yii::$app->db->createCommand()->insert('tbl_control_volumenxclienteS',[
                                    'idservicio' => $txtArbolId,
                                    'idtc' => $varIdCorte,
                                    'mesyear' => $varMesYear,
                                    'cantidadvalorS' => $txtSumCount,
                                    'fechacreacion' => $txtfechacreacion,
                                    'anuladovxcS' => $txtanulado,
                                ])->execute();                                        
                            }
                        }                       
                    }else{
                        $querys3 =  new Query;
                        $querys3    ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                                        'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 3');
                                    //->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command3 = $querys3->createCommand();
                        $query3 = $command3->queryAll();  

                        foreach ($query3 as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];

                            $varDateBegin1 = $varMesYear;
                            $varDateBegin = $varDateBegin1.' 05:00:00';

                            $varDateLast1 = date('Y-m-d',strtotime($varMesYear."+ 1 month"));
                            $varDateLast = $varDateLast1.' 05:00:00';

                            $txtListService = Yii::$app->db->createCommand("select * from tbl_dashboardservicios where arbol_id = :txtArbolId and anulado = 0")
                            ->bindValue(':txtArbolId', $txtArbolId)
                            ->queryAll();

                            $txtSumCount = 0;
                            foreach ($txtListService as $key => $value) {
                                $vartxtService = $value['clientecategoria'];

                                if ($varMesYear < '2020-01-01') {
                                    $querys33 = new Query;
                                    $querys33   ->select(['tbl_dashboardspeechcalls.idcategoria'])
                                                ->from('tbl_dashboardspeechcalls')
                                                ->where(['like','tbl_dashboardspeechcalls.servicio',':vartxtService'])
                                                ->andwhere('tbl_dashboardspeechcalls.idcategoria = :txtCategoria')
                                                ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                                ->andwhere('tbl_dashboardspeechcalls.anulado = :txtanulado')
                                                ->addParams([':vartxtService' => $vartxtService])
                                                ->addParams([':txtCategoria' => $txtCategoria])
                                                ->addParams([':varDateBegin' => $varDateBegin])
                                                ->addParams([':varDateLast' => $varDateLast])
                                                ->addParams([':txtanulado' => $txtanulado]);
                                    $command33 = $querys33->createCommand();
                                    $query33 = $command33->queryAll();  

                                    $txtvarCount33 = count($query33); 

                                    $txtSumCount = $txtSumCount + $txtvarCount33;
                                }else{
                                    $querys33 = new Query;
                                    $querys33   ->select(['tbl_dashboardspeechcalls.idcategoria'])
                                                ->from('tbl_dashboardspeechcalls')
                                                ->where(['like','tbl_dashboardspeechcalls.servicio',':vartxtService'])
                                                ->andwhere('tbl_dashboardspeechcalls.idcategoria = :txtCategoria2 ')
                                                ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada', ':varDateBegin 00:00:00', ':varDateLast 23:59:59'])
                                                ->andwhere('tbl_dashboardspeechcalls.anulado = :txtanulado')
                                                ->addParams([':vartxtService' => $vartxtService])
                                                ->addParams([':txtCategoria2' => $txtCategoria2])
                                                ->addParams([':varDateBegin' => $varDateBegin])
                                                ->addParams([':varDateLast' => $varDateLast])
                                                ->addParams([':txtanulado' => $txtanulado]);
                                    $command33 = $querys33->createCommand();
                                    $query33 = $command33->queryAll();  

                                    $txtvarCount33 = count($query33); 

                                    $txtSumCount = $txtSumCount + $txtvarCount33;
                                }
                            }

                            $verContarList3 = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxclienteS where idservicio = :txtArbolId and idtc = :varIdCorte and anuladovxcS = 0')
                            ->bindValue(':txtArbolId', $txtArbolId)
                            ->bindValue(':varIdCorte', $varIdCorte)
                            ->queryScalar();

                            if ($verContarList3 == 0) {
                                Yii::$app->db->createCommand()->insert('tbl_control_volumenxclienteS',[
                                    'idservicio' => $txtArbolId,
                                    'idtc' => $varIdCorte,
                                    'mesyear' => $varMesYear,
                                    'cantidadvalorS' => $txtSumCount,
                                    'fechacreacion' => $txtfechacreacion,
                                    'anuladovxcS' => $txtanulado,
                                ])->execute();                                        
                            }
                        } 
                    }
                }                
            }

            return $this->redirect(['index']);

        } 

        public function actionVertotalidadqa(){

            return $this->renderAjax('vertotalidadqa');
        }

        public function actionVertotalidadsp(){

            return $this->renderAjax('vertotalidadsp');
        }  

        public function actionVertotalidadgeneral(){

            return $this->renderAjax('vertotalidadgeneral');
        }  

        public function actionVerclienteqa($idservicio){       
            $txtIdSevicio = $idservicio;

            $varServicio = Yii::$app->db->createCommand('select name from tbl_arbols where id = :txtIdSevicio and activo = 0')
            ->bindValue(':txtIdSevicio', $txtIdSevicio)
            ->queryScalar();

            $varFormularios = Yii::$app->db->createCommand('select id, name from tbl_arbols where arbol_id = :txtIdSevicio and activo = 0')
            ->bindValue(':txtIdSevicio', $txtIdSevicio)
            ->queryAll();
  

            return $this->render('verclienteqa',[
                'varFormularios' => $varFormularios,
                'varServicio' => $varServicio,
                'txtIdSevicio' => $txtIdSevicio,
                ]);
        } 

        public function actionVerclientesp($idservicio){      
            $txtIdSevicio = $idservicio;

            $varServicio = Yii::$app->db->createCommand('select name from tbl_arbols where id = :txtIdSevicio and activo = 0')
            ->bindValue(':txtIdSevicio', $txtIdSevicio)
            ->queryScalar();

        $varFormularios = Yii::$app->db->createCommand('select nombreservicio, clientecategoria from tbl_dashboardservicios where arbol_id = :txtIdSevicio and anulado = 0')
        ->bindValue(':txtIdSevicio', $txtIdSevicio)
        ->queryAll();
  

            return $this->render('verclientesp',[
                'varFormularios' => $varFormularios,
                'varServicio' => $varServicio,
                'txtIdSevicio' => $txtIdSevicio,
                ]);
        }  

        public function actionDatosformularios($idPcrc){
            $txtIdSevicio = $idPcrc; 

            $varFormularios = Yii::$app->db->createCommand('select id, name from tbl_arbols where arbol_id = :txtIdSevicio and activo = 0')
            ->bindValue(':txtIdSevicio', $txtIdSevicio)
            ->queryAll();

            return $this->renderAjax('datosformularios',[
                'txtIdSevicio' => $txtIdSevicio,
                'varFormularios' => $varFormularios,
                ]);
        }

        public function actionDatosformularios1($idPcrc){
            $txtIdSevicio = $idPcrc;

            $varFormularios = Yii::$app->db->createCommand('select nombreservicio, clientecategoria from tbl_dashboardservicios where arbol_id = :txtIdSevicio and anulado = 0')
            ->bindValue(':txtIdSevicio', $txtIdSevicio)
            ->queryAll();
  

            return $this->renderAjax('datosformularios1',[
                'varFormularios' => $varFormularios,
                'txtIdSevicio' => $txtIdSevicio,
                ]);
        }

        public function actionParametrizardatosdayqa(){
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");

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

            $varServicios = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")->queryAll();

            foreach ($varServicios as $key => $value) {
                $varIdServicios = $value['id'];

                if ($varIdServicios == '118' || $varIdServicios == '17') {
                    $querys =  new Query;
                    $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                ->from('tbl_tipocortes')
                                ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                        'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                ->where('tbl_grupo_cortes.idgrupocorte = 1');
                                //->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                        $varIdCorte = $value['idtc'];
                        $varMesYear = $value['mesyear'];

                        $varNDias = date('t',strtotime($varMesYear));
                        $varYear = date('Y',strtotime($varMesYear));
                        $varMes = date('m',strtotime($varMesYear));

                        $varDiaActual = date('j');
                        $varMesActual = date('m');
                        $varYearActual = date('Y');

                        $varDiasTranscurridos = 0;
                        for ($i=1; $i <= $varNDias; $i++) { 
                            if ($i == '1' || $i == '2' || $i == '3' || $i == '4' || $i == '5' || $i == '6' || $i == '7' || $i == '8' || $i == '9') {
                                $varDiasTranscurridos = '0'.$i;
                            }else{
                                $varDiasTranscurridos =  $i;
                            }                         

                            $varFecha = $varYear.'-'.$varMes.'-'.$varDiasTranscurridos;

                            $querys =  new Query;
                            $querys     ->select(['tbl_ejecucionformularios.created','tbl_usuarios.usua_id'])->distinct()
                                        ->from('tbl_ejecucionformularios')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                                'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                                'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                        ->join('LEFT OUTER JOIN', 'tbl_roles',
                                                'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                        ->where(['between','tbl_ejecucionformularios.created', ':varFecha 00:00:00', ':varFecha 23:59:59'])
                                        ->andwhere('tbl_arbols.arbol_id = :varIdServicios')
                                        ->andwhere(['in','tbl_dimensions.id',[1, 11]])
                                        ->andwhere(['in','tbl_roles.role_id',[272]])
                                        ->addParams([':varFecha' => $varFecha])
                                        ->addParams([':varIdServicios' => $varIdServicios]);                                    
                            $command = $querys->createCommand();
                            $queryss = $command->queryAll(); 

                            $query = count($queryss);  

                            $varFechaActual = $varYearActual.'-'.$varMesActual.'-'.$varDiaActual;

                            if ($varFecha < $varFechaActual) {
                                
                                $varVerificar = Yii::$app->db->createCommand('select * from tbl_control_volumenxclientedq where idservicio = :varIdServicios and idtc = :varIdCorte and fechavaloracion = :varFecha and anuladovxc = 0')
                                ->bindValue(':varIdServicios', $varIdServicios)
                                ->bindValue(':varIdCorte', $varIdCorte)
                                ->bindValue(':varFecha', $varFecha)
                                ->queryScalar();

                                if ($varVerificar == 0) {
                                    Yii::$app->db->createCommand()->insert('tbl_control_volumenxclientedq',[
                                        'idservicio' => $varIdServicios,
                                        'idtc' => $varIdCorte,
                                        'fechavaloracion' => $varFecha,
                                        'mesyear' => $varMesYear,
                                        'cantidadvalor' => $query,
                                        'fechacreacion' => $txtfechacreacion,
                                        'anuladovxc' => $txtanulado,
                                    ])->execute(); 
                                }
                            }
                        }

                    }
                }else{
                    if ($varIdServicios == '237' || $varIdServicios == '1358' || $varIdServicios == '105' || $varIdServicios == '8' || $varIdServicios == '99') {
                        $querys =  new Query;
                        $querys         ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                        ->from('tbl_tipocortes')
                                        ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                                'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                        ->where('tbl_grupo_cortes.idgrupocorte = 2');
                                        //->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];

                            $varNDias = date('t',strtotime($varMesYear));
                            $varYear = date('Y',strtotime($varMesYear));
                            $varMes = date('m',strtotime($varMesYear));

                            $varDiaActual = date('j');
                            $varMesActual = date('m');
                            $varYearActual = date('Y');

                            $varDiasTranscurridos = 0;
                            for ($i=1; $i <= $varNDias; $i++) { 
                                if ($i == '1' || $i == '2' || $i == '3' || $i == '4' || $i == '5' || $i == '6' || $i == '7' || $i == '8' || $i == '9') {
                                    $varDiasTranscurridos = '0'.$i;
                                }else{
                                    $varDiasTranscurridos =  $i;
                                }                            

                                $varFecha = $varYear.'-'.$varMes.'-'.$varDiasTranscurridos;

                                $querys =  new Query;
                                $querys     ->select(['tbl_ejecucionformularios.created','tbl_usuarios.usua_id'])->distinct()
                                            ->from('tbl_ejecucionformularios')
                                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                        'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id')
                                            ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                                        'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                            ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                                        'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                            ->join('LEFT OUTER JOIN', 'tbl_roles',
                                                        'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                            ->where(['between','tbl_ejecucionformularios.created', ':varFecha 00:00:00', ':varFecha 23:59:59'])
                                            ->andwhere('tbl_arbols.arbol_id = :varIdServicios')
                                            ->andwhere(['in','tbl_dimensions.id',[1, 11, 2]])
                                            ->andwhere(['in','tbl_roles.role_id',[272]])
                                            ->addParams([':varFecha' => $varFecha])
                                            ->addParams([':varIdServicios' => $varIdServicios]);
                                $command = $querys->createCommand();
                                $queryss = $command->queryAll(); 

                                $query = count($queryss); 

                                $varFechaActual = $varYearActual.'-'.$varMesActual.'-'.$varDiaActual;

                                if ($varFecha < $varFechaActual) {
                                    $varVerificar = Yii::$app->db->createCommand('select * from tbl_control_volumenxclientedq where idservicio = :varIdServicios and idtc = :varIdCorte and fechavaloracion = :varFecha and anuladovxc = 0')
                                    ->bindValue(':varIdServicios', $varIdServicios)
                                    ->bindValue(':varIdCorte', $varIdCorte)
                                    ->bindValue(':varFecha', $varFecha)
                                    ->queryScalar();

                                    if ($varVerificar == 0) {
                                        Yii::$app->db->createCommand()->insert('tbl_control_volumenxclientedq',[
                                            'idservicio' => $varIdServicios,
                                            'idtc' => $varIdCorte,
                                            'fechavaloracion' => $varFecha,
                                            'mesyear' => $varMesYear,
                                            'cantidadvalor' => $query,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladovxc' => $txtanulado,
                                        ])->execute();
                                    }
                                }
                            }
                        }
                    }else{
                        $querys =  new Query;
                        $querys         ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                        ->from('tbl_tipocortes')
                                        ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                                'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                        ->where('tbl_grupo_cortes.idgrupocorte = 3');
                                        //->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varDateBegin = $value['fechainiciotc'];
                            $varDateLast = $value['fechafintc'];
                            $varMesYear = $value['mesyear'];

                            $varNDias = date('t',strtotime($varMesYear));
                            $varYear = date('Y',strtotime($varMesYear));
                            $varMes = date('m',strtotime($varMesYear));

                            $varDiaActual = date('j');
                            $varMesActual = date('m');
                            $varYearActual = date('Y');

                            $varDiasTranscurridos = 0;

                            for ($i=1; $i <= $varNDias; $i++) { 
                                if ($i == '1' || $i == '2' || $i == '3' || $i == '4' || $i == '5' || $i == '6' || $i == '7' || $i == '8' || $i == '9') {
                                    $varDiasTranscurridos = '0'.$i;
                                }else{
                                    $varDiasTranscurridos =  $i;
                                }                            

                                $varFecha = $varYear.'-'.$varMes.'-'.$varDiasTranscurridos;

                                $querys =  new Query;
                                $querys     ->select(['tbl_ejecucionformularios.created','tbl_usuarios.usua_id'])->distinct()
                                            ->from('tbl_ejecucionformularios')
                                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                            'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                            'tbl_usuarios.usua_id = tbl_ejecucionformularios.usua_id')
                                            ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                                                            'tbl_dimensions.id = tbl_ejecucionformularios.dimension_id')
                                            ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                                            'tbl_ejecucionformularios.usua_id = rel_usuarios_roles.rel_usua_id')
                                            ->join('LEFT OUTER JOIN', 'tbl_roles',
                                                            'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                            ->where(['between','tbl_ejecucionformularios.created', ':varFecha 00:00:00', ':varFecha 23:59:59'])
                                            ->andwhere('tbl_arbols.arbol_id = :varIdServicios')
                                            ->andwhere(['in','tbl_dimensions.id',[1, 11]])
                                            ->andwhere(['in','tbl_roles.role_id',[272]])
                                            ->addParams([':varFecha' => $varFecha])
                                            ->addParams([':varIdServicios' => $varIdServicios]);
                                $command = $querys->createCommand();
                                $queryss = $command->queryAll(); 

                                $query = count($queryss);

                                $varFechaActual = $varYearActual.'-'.$varMesActual.'-'.$varDiaActual;

                                if ($varFecha < $varFechaActual) {
                                    $varVerificar = Yii::$app->db->createCommand("select * from tbl_control_volumenxclientedq where idservicio = :varIdServicios and idtc = :varIdCorte and fechavaloracion = :varFecha and anuladovxc = 0")
                                    ->bindValue(':varIdServicios', $varIdServicios)
                                    ->bindValue(':varIdCorte', $varIdCorte)
                                    ->bindValue(':varFecha', $varFecha)
                                    ->queryScalar();

                                    if ($varVerificar == 0) {
                                        Yii::$app->db->createCommand()->insert('tbl_control_volumenxclientedq',[
                                            'idservicio' => $varIdServicios,
                                            'idtc' => $varIdCorte,
                                            'fechavaloracion' => $varFecha,
                                            'mesyear' => $varMesYear,
                                            'cantidadvalor' => $query,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladovxc' => $txtanulado,
                                        ])->execute();
                                    }
                                }

                            }
                        }
                    }
                }
            }

            return $this->redirect(['index']);
        }


        public function actionParametrizarencuestasdq(){
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");

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

            $varServicios = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")->queryAll();

            foreach ($varServicios as $key => $value) {
                $varIdServicios = $value['id'];

                if ($varIdServicios == '118' || $varIdServicios == '17') {
                    $querys =  new Query;
                    $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                ->from('tbl_tipocortes')
                                ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                        'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                ->where('tbl_grupo_cortes.idgrupocorte = 1');
                                // ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                        $varIdCorte = $value['idtc'];
                        $varMesYear = $value['mesyear'];

                        $varNDias = date('t',strtotime($varMesYear));
                        $varYear = date('Y',strtotime($varMesYear));
                        $varMes = date('m',strtotime($varMesYear));

                        $varDiaActual = date('j');
                        $varMesActual = date('m');
                        $varYearActual = date('Y');

                        $varDiasTranscurridos = 0;
                        for ($i=1; $i <= $varNDias; $i++) { 
                            if ($i == '1' || $i == '2' || $i == '3' || $i == '4' || $i == '5' || $i == '6' || $i == '7' || $i == '8' || $i == '9') {
                                $varDiasTranscurridos = '0'.$i;
                            }else{
                                $varDiasTranscurridos =  $i;
                            }                         

                            $varFecha = $varYear.'-'.$varMes.'-'.$varDiasTranscurridos;
                            $querys = new Query;
                            $querys     ->select(['sum((select count(*) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varFecha 00:00:00 and :varFecha 23:59:59 and tbl_base_satisfaccion.pcrc = tbl_arbols.id and tbl_base_satisfaccion.tipo_inbox in (NORMAL))) as Sumatotal'])->distinct()
                                        ->from('tbl_arbols')
                                        ->where('tbl_arbols.arbol_id = :varIdServicios')
                                        ->andwhere("tbl_arbols.activo = 0")
                                        ->addParams([':varFecha' => $varFecha])
                                        ->addParams([':varIdServicios' => $varIdServicios]);                                    
                            $command = $querys->createCommand();
                            $queryss = $command->queryScalar(); 

                            $varFechaActual = $varYearActual.'-'.$varMesActual.'-'.$varDiaActual;

                            if ($varFecha < $varFechaActual) {

                                $varService = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxencuestasdq where idservicio = :varIdServicios and idtc = :varIdCorte and fechaencuesta = :varFecha and anuladovxedq = 0')
                                ->bindValue(':varIdServicios', $varIdServicios)
                                ->bindValue(':varIdCorte', $varIdCorte)
                                ->bindValue(':varFecha', $varFecha)
                                ->queryScalar();

                                if ($varService == 0) {
                                    Yii::$app->db->createCommand()->insert('tbl_control_volumenxencuestasdq',[
                                            'idservicio' => $varIdServicios,
                                            'idtc' => $varIdCorte,
                                            'fechaencuesta' => $varFecha,
                                            'mesyear' => $varMesYear,
                                            'cantidadvalor' => $queryss,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anuladovxedq' => $txtanulado,
                                        ])->execute();
                                }
                            }
                        }

                        
                    }
                }else{
                    if($varIdServicios == '237' || $varIdServicios == '1358' || $varIdServicios == '105' || $varIdServicios == '8' || $varIdServicios == '99'){
                        $querys =  new Query;
                        $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 2');
                                    //->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];

                            $varNDias = date('t',strtotime($varMesYear));
                            $varYear = date('Y',strtotime($varMesYear));
                            $varMes = date('m',strtotime($varMesYear));

                            $varDiaActual = date('j');
                            $varMesActual = date('m');
                            $varYearActual = date('Y');

                            $varDiasTranscurridos = 0;
                            for ($i=1; $i <= $varNDias; $i++) { 
                                if ($i == '1' || $i == '2' || $i == '3' || $i == '4' || $i == '5' || $i == '6' || $i == '7' || $i == '8' || $i == '9') {
                                    $varDiasTranscurridos = '0'.$i;
                                }else{
                                    $varDiasTranscurridos =  $i;
                                }                         

                                $varFecha = $varYear.'-'.$varMes.'-'.$varDiasTranscurridos;

                                $querys = new Query;
                                $querys     ->select(['sum((select count(*) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varFecha 00:00:00 and :varFecha 23:59:59 and tbl_base_satisfaccion.pcrc = tbl_arbols.id and tbl_base_satisfaccion.tipo_inbox in (NORMAL))) as Sumatotal'])->distinct()
                                            ->from('tbl_arbols')
                                            ->where('tbl_arbols.arbol_id = :varIdServicios')
                                            ->andwhere("tbl_arbols.activo = 0")
                                            ->addParams([':varFecha' => $varFecha])
                                            ->addParams([':varIdServicios' => $varIdServicios]);
                                $command = $querys->createCommand();
                                $queryss = $command->queryScalar();

                                $varFechaActual = $varYearActual.'-'.$varMesActual.'-'.$varDiaActual;

                                if ($varFecha < $varFechaActual) {

                                    $varService = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxencuestasdq where idservicio = :varIdServicios and idtc = :varIdCorte and fechaencuesta = :varFecha and anuladovxedq = 0')
                                    ->bindValue(':varIdServicios', $varIdServicios)
                                    ->bindValue(':varIdCorte', $varIdCorte)
                                    ->bindValue(':varFecha', $varFecha)
                                    ->queryScalar();

                                    if ($varService == 0) {
                                        Yii::$app->db->createCommand()->insert('tbl_control_volumenxencuestasdq',[
                                                'idservicio' => $varIdServicios,
                                                'idtc' => $varIdCorte,
                                                'fechaencuesta' => $varFecha,
                                                'mesyear' => $varMesYear,
                                                'cantidadvalor' => $queryss,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladovxedq' => $txtanulado,
                                            ])->execute();
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
                                    ->where('tbl_grupo_cortes.idgrupocorte = 3');
                                    // ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                        $command = $querys->createCommand();
                        $query = $command->queryAll();

                        foreach ($query as $key => $value) {
                            $varIdCorte = $value['idtc'];
                            $varMesYear = $value['mesyear'];

                            $varNDias = date('t',strtotime($varMesYear));
                            $varYear = date('Y',strtotime($varMesYear));
                            $varMes = date('m',strtotime($varMesYear));

                            $varDiaActual = date('j');
                            $varMesActual = date('m');
                            $varYearActual = date('Y');

                            $varDiasTranscurridos = 0;
                            for ($i=1; $i <= $varNDias; $i++) { 
                                if ($i == '1' || $i == '2' || $i == '3' || $i == '4' || $i == '5' || $i == '6' || $i == '7' || $i == '8' || $i == '9') {
                                    $varDiasTranscurridos = '0'.$i;
                                }else{
                                    $varDiasTranscurridos =  $i;
                                }                         

                                $varFecha = $varYear.'-'.$varMes.'-'.$varDiasTranscurridos;
                                $querys = new Query;
                                $querys     ->select(['sum((select count(*) from tbl_base_satisfaccion where tbl_base_satisfaccion.created between :varFecha 00:00:00 and :varFecha 23:59:59 and tbl_base_satisfaccion.pcrc = tbl_arbols.id and tbl_base_satisfaccion.tipo_inbox in (NORMAL))) as Sumatotal'])->distinct()
                                            ->from('tbl_arbols')
                                            ->where('tbl_arbols.arbol_id = :varIdServicios')
                                            ->andwhere("tbl_arbols.activo = 0")
                                            ->addParams([':varFecha' => $varFecha])
                                            ->addParams([':varIdServicios' => $varIdServicios]);                                    
                                $command = $querys->createCommand();
                                $queryss = $command->queryScalar();

                                $varFechaActual = $varYearActual.'-'.$varMesActual.'-'.$varDiaActual;

                                if ($varFecha < $varFechaActual) {

                                    $varService = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxencuestasdq where idservicio = :varIdServicios and idtc = :varIdCorte and fechaencuesta = :varFecha and anuladovxedq = 0')
                                    ->bindValue(':varIdServicios', $varIdServicios)
                                    ->bindValue(':varIdCorte', $varIdCorte)
                                    ->bindValue(':varFecha', $varFecha)
                                    ->queryScalar();

                                    if ($varService == 0) {
                                        Yii::$app->db->createCommand()->insert('tbl_control_volumenxencuestasdq',[
                                                'idservicio' => $varIdServicios,
                                                'idtc' => $varIdCorte,
                                                'fechaencuesta' => $varFecha,
                                                'mesyear' => $varMesYear,
                                                'cantidadvalor' => $queryss,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladovxedq' => $txtanulado,
                                            ])->execute();
                                    }
                                }
                            }
                            
                        }
                    }                    
                }
            } 
            return $this->redirect(['index']);           
     }  


    public function actionParametrizardatosdaysp() {
        $txtanulado = 0;
        $txtfechacreacion = date("Y-m-d");

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

        $varServicios = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")->queryAll();

        foreach ($varServicios as $key => $value) {
            $varIdServicios = $value['id'];

            if ($varIdServicios == '118' || $varIdServicios == '17') {
                $querys =  new Query;
                $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                ->from('tbl_tipocortes')
                                ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                        'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                ->where('tbl_grupo_cortes.idgrupocorte = 1');
                                // ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                $command = $querys->createCommand();
                $query = $command->queryAll();

                foreach ($query as $key => $value) {
                    $varIdCorte = $value['idtc'];
                    $varMesYear = $value['mesyear'];

                    $varNDias = date('t',strtotime($varMesYear));
                    $varYear = date('Y',strtotime($varMesYear));
                    $varMes = date('m',strtotime($varMesYear));

                    $varDiaActual = date('j');
                    $varMesActual = date('m');
                    $varYearActual = date('Y');

                    $varDiasTranscurridos = 0;

                    if ($varMesYear >= '2019-10-01') {
                        for ($i=1; $i <= $varNDias; $i++) { 
                            if ($i == '1' || $i == '2' || $i == '3' || $i == '4' || $i == '5' || $i == '6' || $i == '7' || $i == '8' || $i == '9') {
                                    $varDiasTranscurridos = '0'.$i;
                                }else{
                                    $varDiasTranscurridos =  $i;
                                }                         

                                $varFecha = $varYear.'-'.$varMes.'-'.$varDiasTranscurridos;
                                $varFinFecha = date('Y-m-d',strtotime($varFecha."+ 1 days"));

                                $varListData = Yii::$app->db->createCommand("select distinct a.id, sp.id_dp_clientes, sc.programacategoria, sp.rn, sp.ext, sp.usuared, sp.comentarios from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc inner join tbl_speech_servicios ss on sp.id_dp_clientes = ss.id_dp_clientes inner join tbl_arbols a on ss.arbol_id = a.id where a.id = :varIdServicios and a.activo = 0")
                                ->bindValue(':varIdServicios', $varIdServicios)
                                ->queryAll();

                                if ($varListData != null) {
                                    $varArrayProgram = array();
                                    $varArrayparams = array();
                                    $varArbol = null;
                                    $varSpeech = null;
                                    foreach ($varListData as $key => $value) {

                                        array_push($varArrayProgram, $value['programacategoria']);
                                        array_push($varArrayparams, $value['rn'], $value['ext'], $value['usuared'], $value['comentarios']);
                                    }
                                    $txtSerivicios = implode("', '", $varArrayProgram);
                                    $txtExtensiones = implode("', '", $varArrayparams);

                                    $txtTotalLlamadas = Yii::$app->db->createCommand('select count(*) from tbl_dashboardspeechcalls where anulado = 0 and servicio in (:txtSerivicios) and fechallamada between :varFecha 05:00:00 and :varFinFecha 05:00:00 and idcategoria = 1105 and extension in (:txtExtensiones)')
                                    ->bindValue(':txtSerivicios', $txtSerivicios)
                                    ->bindValue(':varFecha', $varFecha)
                                    ->bindValue(':varFinFecha', $varFinFecha)
                                    ->bindValue(':txtExtensiones', $txtExtensiones)
                                    ->queryScalar();

                                    $varFechaActual = $varYearActual.'-'.$varMesActual.'-'.$varDiaActual;

                                    if ($varFecha < $varFechaActual) {
                                        $verContarList = Yii::$app->db->createCommand("select count(*) from tbl_control_volumenxclienteds where idservicio = :varIdServicios and idtc = :varIdCorte and fechavaloracion = :varFecha and anuladovxcs = 0")
                                        ->bindValue(':varIdServicios', $varIdServicios)
                                        ->bindValue(':varIdCorte', $varIdCorte)
                                        ->bindValue(':varFecha', $varFecha)
                                        ->queryScalar();

                                        if ($verContarList == 0) {
                                            Yii::$app->db->createCommand()->insert('tbl_control_volumenxclienteds',[
                                                'idservicio' => $varIdServicios,
                                                'idtc' => $varIdCorte,
                                                'fechavaloracion' => $varFecha,
                                                'mesyear' => $varMesYear,
                                                'cantidadvalor' => $txtTotalLlamadas,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladovxcs' => $txtanulado,
                                            ])->execute();                                        
                                        }
                                    }
                                }
                                
                        }
                    }
                    
                }
            }else{
                if ($varIdServicios == '237' || $varIdServicios == '1358' || $varIdServicios == '105' || $varIdServicios == '8' || $varIdServicios == '99' || $varIdServicios == '675') {
                    $querys =  new Query;
                    $querys     ->select(['tbl_tipocortes.idtc', 'tbl_tipocortes.tipocortetc', 'tbl_tipocortes.fechainiciotc', 'tbl_tipocortes.fechafintc', 'tbl_tipocortes.mesyear'])->distinct()
                                    ->from('tbl_tipocortes')
                                    ->join('LEFT OUTER JOIN', 'tbl_grupo_cortes',
                                            'tbl_tipocortes.idgrupocorte = tbl_grupo_cortes.idgrupocorte')
                                    ->where('tbl_grupo_cortes.idgrupocorte = 2');
                                    // ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                        $varIdCorte = $value['idtc'];
                        $varMesYear = $value['mesyear'];

                        $varNDias = date('t',strtotime($varMesYear));
                        $varYear = date('Y',strtotime($varMesYear));
                        $varMes = date('m',strtotime($varMesYear));

                        $varDiaActual = date('j');
                        $varMesActual = date('m');
                        $varYearActual = date('Y');

                        $varDiasTranscurridos = 0;

                        if ($varMesYear >= '2019-10-01') {
                            for ($i=1; $i <= $varNDias; $i++) { 
                                if ($i == '1' || $i == '2' || $i == '3' || $i == '4' || $i == '5' || $i == '6' || $i == '7' || $i == '8' || $i == '9') {
                                    $varDiasTranscurridos = '0'.$i;
                                }else{
                                    $varDiasTranscurridos = $i;
                                }

                                $varFecha = $varYear.'-'.$varMes.'-'.$varDiasTranscurridos;
                                $varFinFecha = date('Y-m-d',strtotime($varFecha."+ 1 days"));

                                $varListData = Yii::$app->db->createCommand("select distinct a.id, sp.id_dp_clientes, sc.programacategoria, sp.rn, sp.ext, sp.usuared, sp.comentarios from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc inner join tbl_speech_servicios ss on sp.id_dp_clientes = ss.id_dp_clientes inner join tbl_arbols a on ss.arbol_id = a.id where a.id = :varIdServicios and a.activo = 0")
                                ->bindValue(':varIdServicios', $varIdServicios)
                                ->queryAll();

                                if ($varListData != null) {
                                    $varArrayProgram = array();
                                    $varArrayparams = array();
                                    $txtArbol = null;
                                    $txtSpeech = null;
                                    $varArbol = null;
                                    $varSpeech = null;
                                    foreach ($varListData as $key => $value) {
                                        $varArbol = $value['id'];
                                        $varSpeech = $value['id_dp_clientes'];

                                        array_push($varArrayProgram, $value['programacategoria']);
                                        array_push($varArrayparams, $value['rn'], $value['ext'], $value['usuared'], $value['comentarios']);
                                    }

                                    $txtArbol = $varArbol;
                                    $txtSpeech = $varSpeech;

                                    $txtSerivicios = implode("', '", $varArrayProgram);
                                    $txtExtensiones = implode("', '", $varArrayparams);

                                    $txtTotalLlamadas = Yii::$app->db->createCommand('select count(*) from tbl_dashboardspeechcalls where anulado = 0 and servicio in (:txtSerivicios) and fechallamada between :varFecha 05:00:00 and :varFinFecha 05:00:00 and idcategoria = 1105 and extension in (:txtExtensiones)')
                                    ->bindValue(':txtSerivicios', $txtSerivicios)
                                    ->bindValue(':varFecha', $varFecha)
                                    ->bindValue(':varFinFecha', $varFinFecha)
                                    ->bindValue(':txtExtensiones', $txtExtensiones)
                                    ->queryScalar();

                                    $varFechaActual = $varYearActual.'-'.$varMesActual.'-'.$varDiaActual;

                                    if ($varFecha < $varFechaActual) {
                                        $verContarList = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxclienteds where idservicio = :varIdServicios and idtc = :varIdCorte and fechavaloracion = :varFecha and anuladovxcs = 0')
                                        ->bindValue(':varIdServicios', $varIdServicios)
                                        ->bindValue(':varIdCorte', $varIdCorte)
                                        ->bindValue(':varFecha', $varFecha)
                                        ->queryScalar();

                                        if ($verContarList == 0) {
                                            Yii::$app->db->createCommand()->insert('tbl_control_volumenxclienteds',[
                                                'idservicio' => $varIdServicios,
                                                'idtc' => $varIdCorte,
                                                'fechavaloracion' => $varFecha,
                                                'mesyear' => $varMesYear,
                                                'cantidadvalor' => $txtTotalLlamadas,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladovxcs' => $txtanulado,
                                            ])->execute();                                        
                                        }
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
                                    ->where('tbl_grupo_cortes.idgrupocorte = 2');
                                    // ->andwhere(['not like', 'tbl_tipocortes.tipocortetc', $txtMes]);                    
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                        $varIdCorte = $value['idtc'];
                        $varMesYear = $value['mesyear'];

                        $varNDias = date('t',strtotime($varMesYear));
                        $varYear = date('Y',strtotime($varMesYear));
                        $varMes = date('m',strtotime($varMesYear));

                        $varDiaActual = date('j');
                        $varMesActual = date('m');
                        $varYearActual = date('Y');

                        $varDiasTranscurridos = 0;

                        if ($varMesYear >= '2019-10-01') {
                            for ($i=1; $i <= $varNDias; $i++) { 
                                if ($i == '1' || $i == '2' || $i == '3' || $i == '4' || $i == '5' || $i == '6' || $i == '7' || $i == '8' || $i == '9') {
                                    $varDiasTranscurridos = '0'.$i;
                                }else{
                                    $varDiasTranscurridos = $i;
                                }

                                $varFecha = $varYear.'-'.$varMes.'-'.$varDiasTranscurridos;
                                $varFinFecha = date('Y-m-d',strtotime($varFecha."+ 1 days"));

                                $varListData = Yii::$app->db->createCommand('select distinct a.id, sp.id_dp_clientes, sc.programacategoria, sp.rn, sp.ext, sp.usuared, sp.comentarios from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc inner join tbl_speech_servicios ss on sp.id_dp_clientes = ss.id_dp_clientes inner join tbl_arbols a on ss.arbol_id = a.id where a.id = :varIdServicios and a.activo = 0')
                                ->bindValue(':varIdServicios', $varIdServicios)
                                ->queryAll();

                                if ($varListData != null) {
                                    $varArrayProgram = array();
                                    $varArrayparams = array();
                                    $txtArbol = null;
                                    $txtSpeech = null;
                                    $varArbol = null;
                                    $varSpeech = null;
                                    foreach ($varListData as $key => $value) {
                                        $varArbol = $value['id'];
                                        $varSpeech = $value['id_dp_clientes'];

                                        array_push($varArrayProgram, $value['programacategoria']);
                                        array_push($varArrayparams, $value['rn'], $value['ext'], $value['usuared'], $value['comentarios']);
                                    }

                                    $txtArbol = $varArbol;
                                    $txtSpeech = $varSpeech;

                                    $txtSerivicios = implode("', '", $varArrayProgram);
				
                                    $txtExtensiones = implode("', '", $varArrayparams);

                                    $txtTotalLlamadas = Yii::$app->db->createCommand('select count(*) from tbl_dashboardspeechcalls where anulado = 0 and servicio in (:txtSerivicios) and fechallamada between :varFecha 05:00:00 and :varFinFecha 05:00:00 and idcategoria = 1114 and extension in (:txtExtensiones)')
                                    ->bindValue(':txtSerivicios', $txtSerivicios)
                                    ->bindValue(':varFecha', $varFecha)
                                    ->bindValue(':varFinFecha', $varFinFecha)
                                    ->bindValue(':txtExtensiones', $txtExtensiones)
                                    ->queryScalar();

                                    $varFechaActual = $varYearActual.'-'.$varMesActual.'-'.$varDiaActual;

                                    if ($varFecha < $varFechaActual) {
                                        $verContarList = Yii::$app->db->createCommand('select count(*) from tbl_control_volumenxclienteds where idservicio = :varIdServicios and idtc = :varIdCorte and fechavaloracion = :varFecha and anuladovxcs = 0')
                                        ->bindValue(':varIdServicios', $varIdServicios)
                                        ->bindValue(':varIdCorte', $varIdCorte)
                                        ->bindValue(':varFecha', $varFecha)
                                        ->queryScalar();

                                        if ($verContarList == 0) {
                                            Yii::$app->db->createCommand()->insert('tbl_control_volumenxclienteds',[
                                                'idservicio' => $varIdServicios,
                                                'idtc' => $varIdCorte,
                                                'fechavaloracion' => $varFecha,
                                                'mesyear' => $varMesYear,
                                                'cantidadvalor' => $txtTotalLlamadas,
                                                'fechacreacion' => $txtfechacreacion,
                                                'anuladovxcs' => $txtanulado,
                                            ])->execute();                                        
                                        }
                                    }
                                }                                
                            }
                        }
                    }
                }
            }
        }

    }
        



    }

?>
