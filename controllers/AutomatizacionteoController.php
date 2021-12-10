<?php

namespace app\controllers;

use Yii;
use app\models\Tipobloques;
use app\models\TipobloquesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Equipoteo;
use app\models\Equipos;
use app\models\EquiposEvaluados;
use app\models\Evaluados;
use app\models\Usuarios;

class AutomatizacionteoController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    $msg = \Yii::t('app', 'The requested Item could not be found.');
                    Yii::$app->session->setFlash('danger', $msg);
                    $url = \yii\helpers\Url::to(['site/index']);
                    return $action->controller->redirect($url);
                },
                        'rules' => [
                            [
                                'actions' => ['index', 'create', 'update', 'view',
                                    'delete', 'excel', 'simulacion'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos();
                        },
                            ],
                            [
                                'actions' => ['executefromserver', 'backupequipos'],
                                'allow' => true,
                            ],
                        ],
                    ],
                ];
            }

            public function TrimElementArray($element)
            {
                if(!isset($element)){
                    return '';
                }else
                {
                    #code
                }
                
                $element = utf8_decode($element);
                $element = strtolower(trim($element));
                $element = str_replace("�",'n', $element);
                return $element;
            }

            public function arrayEquipoTeo($array)
            {
                $newArray = null;
                foreach ($array as $value) {
                    $newArray[$value['dmeNumeroDocumento']] = $value['dmeNumeroDocumento'];
                }
                return $newArray;
            }

            public function arrayEquipoQA($array)
            {
                $newArray = null;
                foreach ($array as $value) {
                    $newArray[$value['evaluado_id']] = $value['identificacion'];
                }
                return $newArray;
            }

            public function actionExecutefromserver()
            {
                if($_SERVER['REMOTE_ADDR'] == "172.20.73.184" ){
                      $this->actionIndex();
                }else{
                      return $this->redirect(['equipos/index']);
                }            
            }

            /**
             * Lists all Tipobloques models.
             * @return mixed
             */

            public function actionIndex() 
            {
                ini_set('memory_limit', '-1');
                $fecha = date("Y-m-d", strtotime( date("Y-m",time()) ."-01"));
                $EquipoQa = new Equipos();
                $EquipoQaUsuario = $EquipoQa->getEquiposidentificacionlider();
                $EquipoQaUsuarioA = array();
                $EquipoTeo = new Equipoteo();
                $EquipoTeoUsuario = $EquipoTeo->findAllTeo($fecha);
                $EquiposEvaluados = new EquiposEvaluados();
                $UsuariosQA = new Usuarios();
                $LideresTeo = $EquipoTeo->getLideres($fecha); 
                $reporteEquipos = array(); 
                $reporteAgentes = array();

                if(isset($LideresTeo) && Count($LideresTeo) > 1)
                {
                    foreach ($LideresTeo as $lider)
                    {
                        $allNameTeo = isset($lider['empEmpleadoN6']) ? $lider['empEmpleadoN6'] : "";
                        $cliente = isset($lider['Cliente']) ? $lider['Cliente'] : "";
                        $nameTeam = $allNameTeo."_".$cliente;
                        $identifyUser = isset($lider['dmeNumeroDocumentoSup6']) ? $lider['dmeNumeroDocumentoSup6'] : 0;
                        if($EquipoQaUsuario == null)
                        {
                            $filtered_array = null;
                        }else
                        {
                            $filtered_array = array_filter($EquipoQaUsuario, function($val) use($nameTeam, $identifyUser){
                                          return ($val['name']==$nameTeam and trim($val['usua_identificacion'])==$identifyUser);
                                     });      
                        }
                        
                        if($filtered_array)
                        {
                            //Existe el equipo en QA
                            foreach ($filtered_array as $key => $value) 
                            {
                                array_push($EquipoQaUsuarioA, $value);
                                array_push($reporteEquipos, array('name' => $value['name'], 'identificacion' => $lider['dmeNumeroDocumentoSup6'], 'estado' => 'Sin novedad'));
                                unset($EquipoQaUsuario[$key]);
                                break;                            
                            }
                        }else
                        {
                            $filtered = array_filter($EquipoQaUsuario, function($val) use($identifyUser){
                                      return (trim($val['usua_identificacion'])==$identifyUser);
                                 });
                            if($filtered)
                            {
                                foreach ($filtered as $key => $filt) 
                                {
                                    array_push($EquipoQaUsuarioA, array('id' => $filt['id'], 'name' => $nameTeam, 'usua_id' => $filt['usua_id'], 'usua_identificacion' => $lider['dmeNumeroDocumentoSup6']));
                                    array_push($reporteEquipos, array('name' => $nameTeam, 'identificacion' => $lider['dmeNumeroDocumentoSup6'], 'estado' => 'Actualizado')); 
                                    //Impacto BD                               
                                    $EquipoQa->cleanEquipo($filt['id'], $nameTeam, $filt['usua_id']);
                                    unset($EquipoQaUsuario[$key]);
                                    break;
                                }
                            }else{
                                $User = $UsuariosQA->getUserByIdentify($lider['dmeNumeroDocumentoSup6']);
                                if($User){
                                    $NuevoEquipo = new Equipos();
                                    $NuevoEquipo->name = $nameTeam;
                                    $NuevoEquipo->nmumbral_verde = 1;
                                    $NuevoEquipo->nmumbral_amarillo = 1;
                                    $NuevoEquipo->usua_id = $User['usua_id'];
                                    array_push($EquipoQaUsuarioA, array('id' => 0, 'name' => $nameTeam, 'usua_id' => $User['usua_id'], 'usua_identificacion' => $lider['dmeNumeroDocumentoSup6']));
                                    //Impacto BD
                                    $NuevoEquipo->save();
                                    array_push($reporteEquipos, array('name' => $nameTeam, 'identificacion' => $lider['dmeNumeroDocumentoSup6'], 'estado' => 'Nuevo')); 
                                }else{
                                    array_push($reporteEquipos, array('name' => $nameTeam, 'identificacion' => $lider['dmeNumeroDocumentoSup6'], 'estado' => 'No creado')); 
                                }     
                           }
                        }
                    }

                    $EquipoQaUsuario = $EquipoQa->getEquiposidentificacionlider();

                    foreach ($EquipoQaUsuario as $key => $equipo)
                    {

                        $allNameTeo = isset($equipo['name']) ? explode('_', $equipo['name']) : null;
                        $EquipoTeoByUsua = null;
                        $EquipoQaByEquipoId = null;
                        $evaluadosQA = null;
                        $evaluadosTeo = null;
                        if(isset($allNameTeo[1]))
                        {
                            $EquipoTeoByUsua = $EquipoTeo->findEquipoteo($equipo['usua_identificacion'], $fecha, $allNameTeo[1]);
                            $evaluadosTeo = isset($EquipoTeoByUsua) ? $this->arrayEquipoTeo($EquipoTeoByUsua) : null;
                            $evaluadosTeo = $evaluadosTeo != null ? array_map(array($this,'TrimElementArray'),$evaluadosTeo) : null;
                            $EquipoQaByEquipoId = $EquiposEvaluados->getEvaluadosEquipo(isset($equipo['id']) ? intval($equipo['id']) : 0 );
                            $evaluadosQA = isset($EquipoQaByEquipoId) ? $this->arrayEquipoQA($EquipoQaByEquipoId) : null;
                            $evaluadosQA =  $evaluadosQA != null ? array_map(array($this,'TrimElementArray'),$evaluadosQA) : null;
                        }                        
                        if($evaluadosQA != null && $evaluadosTeo != null && Count($evaluadosTeo) >= 1)
                        {
                            foreach ($evaluadosQA as $key => $evaluadoQA) 
                            {
                                 if(!in_array($evaluadoQA, $evaluadosTeo))
                                 {
                                     //Impacto BD            
                                     $EquiposEvaluados->deleteEvaluado($key, $equipo['id']);
                                 }
                                 else{
                                    #code
                                }
                            }
                        }
                        else{
                            #code
                        }
                        if($evaluadosTeo != null)
                        {
                            foreach ($evaluadosTeo as $key => $evaluadoTeo) 
                            {
                                $Evaluado = new Evaluados();
                                $Evaluado = $Evaluado->getEvaluadoByNetUser($evaluadoTeo);
                                if(isset($Evaluado[0]) && Count($Evaluado) == 1)
                                {
                                    if(!isset($evaluadosQA)){
                                        $evaluadosQA = array();
                                    }
                                    else{
                                        #code
                                    }
                                    if(in_array($evaluadoTeo, $evaluadosQA))
                                    {
                                        array_push($reporteAgentes, array('name' => $evaluadoTeo, 'identificacion' => $key, 'estado' => "Sin novedad", 'equipo' => $equipo['name']));
                                    }else
                                    {
                                        $NuevoEvaluado = new EquiposEvaluados();
                                        $NuevoEvaluado->evaluado_id = $Evaluado[0]['id'];
                                        $NuevoEvaluado->equipo_id = $equipo['id'];
                                        //Impacto BD
                                        $NuevoEvaluado->save();
                                        array_push($reporteAgentes, array('name' => $evaluadoTeo, 'identificacion' => $key, 'estado' => "Actualizado", 'equipo' => $equipo['name']));
                                    }
                                }else
                                {
                                    array_push($reporteAgentes, array('name' => $evaluadoTeo, 'identificacion' => $key, 'estado' => "No creado", 'equipo' => $equipo['name']));
                                }
                            }
                        }else{
                            #code
                        }
                    }
                    $this->ExcelReport($reporteEquipos, $reporteAgentes, "Ejecucion");
                    return $this->redirect(['equipos/index']);
                }else{
                    #code
                }
            }


            public function actionSimulacion() 
            {
                ini_set('memory_limit', '-1');
                $fecha = date("Y-m-d", strtotime( date("Y-m",time()) ."-01"));
                $EquipoQa = new Equipos();
                $EquipoQaUsuario = $EquipoQa->getEquiposidentificacionlider();
                $EquipoQaUsuarioA = array();
                $EquipoTeo = new Equipoteo();
                $EquipoTeoUsuario = $EquipoTeo->findAllTeo($fecha);
                $EquiposEvaluados = new EquiposEvaluados();
                $UsuariosQA = new Usuarios();
                $LideresTeo = $EquipoTeo->getLideres($fecha); 
                $reporteEquipos = array(); 
                $reporteAgentes = array();

                if(isset($LideresTeo) && Count($LideresTeo) > 1)
                {
                    foreach ($LideresTeo as $lider)
                    {
                        $allNameTeo = isset($lider['empEmpleadoN6']) ? $lider['empEmpleadoN6'] : "";
                        $cliente = isset($lider['Cliente']) ? $lider['Cliente'] : "";
                        $nameTeam = $allNameTeo."_".$cliente;
                        $identifyUser = isset($lider['dmeNumeroDocumentoSup6']) ? $lider['dmeNumeroDocumentoSup6'] : 0;
                        if($EquipoQaUsuario == null)
                        {
                            $filtered_array = null;
                        }else
                        {
                            $filtered_array = array_filter($EquipoQaUsuario, function($val) use($nameTeam, $identifyUser){
                                          return ($val['name']==$nameTeam and trim($val['usua_identificacion'])==$identifyUser);
                                     });      
                        }
                        
                        if($filtered_array)
                        {
                            //Existe el equipo en QA
                            foreach ($filtered_array as $key => $value) 
                            {
                                array_push($EquipoQaUsuarioA, $value);
                                array_push($reporteEquipos, array('name' => $value['name'], 'identificacion' => $lider['dmeNumeroDocumentoSup6'], 'estado' => 'Sin novedad'));
                                unset($EquipoQaUsuario[$key]);
                                break;                            
                            }
                        }else
                        {
                            $filtered = array_filter($EquipoQaUsuario, function($val) use($identifyUser){
                                      return (trim($val['usua_identificacion'])==$identifyUser);
                                 });
                            if($filtered)
                            {
                                foreach ($filtered as $key => $filt) 
                                {
                                    array_push($EquipoQaUsuarioA, array('id' => $filt['id'], 'name' => $nameTeam, 'usua_id' => $filt['usua_id'], 'usua_identificacion' => $lider['dmeNumeroDocumentoSup6']));
                                    array_push($reporteEquipos, array('name' => $nameTeam, 'identificacion' => $lider['dmeNumeroDocumentoSup6'], 'estado' => 'Actualizado')); 
                                    //Impacto BD                               
                                    //$EquipoQa->cleanEquipo($filt['id'], $nameTeam, $filt['usua_id']);
                                    unset($EquipoQaUsuario[$key]);
                                    break;
                                }
                            }else{
                                $User = $UsuariosQA->getUserByIdentify($lider['dmeNumeroDocumentoSup6']);
                                if($User){
                                    $NuevoEquipo = new Equipos();
                                    $NuevoEquipo->name = $nameTeam;
                                    $NuevoEquipo->nmumbral_verde = 1;
                                    $NuevoEquipo->nmumbral_amarillo = 1;
                                    $NuevoEquipo->usua_id = $User['usua_id'];
                                    array_push($EquipoQaUsuarioA, array('id' => 0, 'name' => $nameTeam, 'usua_id' => $User['usua_id'], 'usua_identificacion' => $lider['dmeNumeroDocumentoSup6']));
                                    //Impacto BD
                                    //$NuevoEquipo->save();
                                    array_push($reporteEquipos, array('name' => $nameTeam, 'identificacion' => $lider['dmeNumeroDocumentoSup6'], 'estado' => 'Nuevo')); 
                                }else{
                                    array_push($reporteEquipos, array('name' => $nameTeam, 'identificacion' => $lider['dmeNumeroDocumentoSup6'], 'estado' => 'No creado')); 
                                }     
                           }
                        }
                    }

                    $EquipoQaUsuarioAux = array();
                    foreach ($EquipoQaUsuarioA as $value) {
                        if(Count($value) == 1)
                        {
                            foreach ($value as $key => $values) {
                                array_push($EquipoQaUsuarioAux, $values);
                            }
                        }else{
                            array_push($EquipoQaUsuarioAux, $value);
                        }
                    }

                    foreach ($EquipoQaUsuarioAux as $key => $equipo)
                    {

                        $allNameTeo = isset($equipo['name']) ? explode('_', $equipo['name']) : null;
                        $EquipoTeoByUsua = null;
                        $EquipoQaByEquipoId = null;
                        $evaluadosQA = null;
                        $evaluadosTeo = null;
                        if(isset($allNameTeo[1]))
                        {
                            $EquipoTeoByUsua = $EquipoTeo->findEquipoteo($equipo['usua_identificacion'], $fecha, $allNameTeo[1]);
                            $evaluadosTeo = isset($EquipoTeoByUsua) ? $this->arrayEquipoTeo($EquipoTeoByUsua) : null;
                            $evaluadosTeo = $evaluadosTeo != null ? array_map(array($this,'TrimElementArray'),$evaluadosTeo) : null;
                            $EquipoQaByEquipoId = $EquiposEvaluados->getEvaluadosEquipo(isset($equipo['id']) ? intval($equipo['id']) : 0 );
                            $evaluadosQA = isset($EquipoQaByEquipoId) ? $this->arrayEquipoQA($EquipoQaByEquipoId) : null;
                            $evaluadosQA =  $evaluadosQA != null ? array_map(array($this,'TrimElementArray'),$evaluadosQA) : null;
                        }else{
                            #code
                        }                        
                        if($evaluadosQA != null && $evaluadosTeo != null && Count($evaluadosTeo) >= 1)
                        {
                            foreach ($evaluadosQA as $key => $evaluadoQA) 
                            {
                                 if(!in_array($evaluadoQA, $evaluadosTeo))
                                 {
                                     //Impacto BD            
                                     //$EquiposEvaluados->deleteEvaluado($key, $equipo['id']);
                                 }
                                 else{
                                    #code
                                }
                            }
                        }else{
                            #code
                        }
                        if($evaluadosTeo != null)
                        {
                            foreach ($evaluadosTeo as $key => $evaluadoTeo) 
                            {
                                   $Evaluado = new Evaluados();
                                   $Evaluado = $Evaluado->getEvaluadoByNetUser($evaluadoTeo);
                                if(isset($Evaluado[0]) && Count($Evaluado) == 1)
                                {
                                    if(!isset($evaluadosQA)){
                                        $evaluadosQA = array();
                                    }
                                    else{
                                        #code
                                    }
                                    if(in_array($evaluadoTeo, $evaluadosQA))
                                    {
                                        array_push($reporteAgentes, array('name' => $evaluadoTeo, 'identificacion' => $key, 'estado' => "Sin novedad", 'equipo' => $equipo['name']));
                                    }else
                                    {
                                        $NuevoEvaluado = new EquiposEvaluados();
                                        $NuevoEvaluado->evaluado_id = $Evaluado[0]['id'];
                                        $NuevoEvaluado->equipo_id = $equipo['id'];
                                        //Impacto BD
                                        //$NuevoEvaluado->save();
                                        array_push($reporteAgentes, array('name' => $evaluadoTeo, 'identificacion' => $key, 'estado' => "Actualizado", 'equipo' => $equipo['name']));
                                    }
                                }else
                                {
                                    array_push($reporteAgentes, array('name' => $evaluadoTeo, 'identificacion' => $key, 'estado' => "No creado", 'equipo' => $equipo['name']));
                                }
                            }
                        }else{
                            #code
                        }
                    }
                    $this->ExcelReport($reporteEquipos, $reporteAgentes, "Simulacion");
                    return $this->redirect(['equipos/index']);
                }
                else{
                    #code
                }
            }

            /**
             * Displays a single Tipobloques model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                return $this->render('view', [
                            'model' => $this->findModel($id),
                ]);
            }


            public function ExcelReport($equipos = array(), $agentes = array(), $motivo = "")
            {
                $phpExc = new \PHPExcel();

                $phpExc->getProperties()
                        ->setCreator("Konecta")
                        ->setLastModifiedBy("Konecta")
                        ->setTitle("Equipos de Evaluados QA")
                        ->setSubject("Equipos")
                        ->setDescription("Documento generado para informar sobre el resultado de la actualización de equipos en QA acordes a la distribucion de personal de Teo")
                        ->setKeywords("Equipos QA");
                $phpExc->setActiveSheetIndex(0);
                
                if(isset($equipos) && Count($equipos) >= 1 && isset($agentes) && Count($agentes) >= 1 )
                {
                    $numCell = 1;
                    $phpExc->getActiveSheet()->setCellValue('A'.$numCell, 'EQUIPO');
                    $phpExc->getActiveSheet()->setCellValue('B'.$numCell, 'ESTADO EQUIPO');
                    $phpExc->getActiveSheet()->setCellValue('C'.$numCell, 'IDENTIFICACION LIDER');
                    $phpExc->getActiveSheet()->setCellValue('D'.$numCell, 'IDENTIFICACION AGENTE');
                    $phpExc->getActiveSheet()->setCellValue('E'.$numCell, 'ESTADO AGENTE');
                    $numCell = $numCell++ + 1;   

                    foreach ($equipos as $key => $valueEquipo)
                    {
                        $nombreEquipo =  isset($valueEquipo['name']) ? $valueEquipo['name'] : "" ;
                        $filtered_array_agentes = array_filter($agentes, function($val) use($nombreEquipo){
                        return ($val['equipo']==$nombreEquipo);
                        });  
                        if($filtered_array_agentes)
                        {
                            foreach ($filtered_array_agentes as $key => $valueAgente)
                            {
                                $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $nombreEquipo);
                                $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $valueEquipo['estado']);
                                $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $valueEquipo['identificacion']);      
                                $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $valueAgente['identificacion']);
                                $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $valueAgente['estado']);
                                $numCell++;
                            }
                        }else{
                            $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $nombreEquipo);
                            $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $valueEquipo['estado']);   
                            $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $valueEquipo['identificacion']);                                       
                            $numCell++;
                        }
                    }
                }else{
                    #code
                }

                $hoy = getdate();
                $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_".$hoy['hours']."_".$hoy['minutes'];
              
                $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                
                $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
                $tmpFile.= ".xls";

                $objWriter->save($tmpFile);

                $message = "<html><body>";
                $message .= "<h3>$hoy: Proceso ejecutado correctamente! </h3>";
                $message .= "</body></html>";

                Yii::$app->mailer->compose()
                        ->setTo(["samanriquej@grupokonecta.com","maciel.guerrero@grupokonecta.com","rdfigueroa@grupokonecta.com","jonathan.arroyave@grupokonecta.com","anmorenoa@grupokonecta.com","diego.montoya@grupokonecta.com"])
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject($motivo.": Automatizacion de equipos de valorados en QA")
                        ->attach($tmpFile)
                        ->setHtmlBody($message)
                        ->send();
            }

            public function actionBackupequipos()
            {
                $equipoQa = new Equipos();
                $EquipoQa = $equipoQa->find()->all();
                $equiposEvaluados = new EquiposEvaluados();
                $EquiposEvaluados = $equiposEvaluados->find()->all();
                $message = "<html><body><h3>";
                foreach ($EquipoQa as $evaluadoTeo) 
                {
                      $message .= "UPDATE tbl_equipos SET name = \"".$evaluadoTeo['name']."\" , usua_id = ".$evaluadoTeo['usua_id']." WHERE id = ".$evaluadoTeo["id"].";<br>";
       
                }
                foreach ($EquiposEvaluados as $equipoEvaluado) 
                {
                      $message .= "UPDATE tbl_equipos_evaluados SET evaluado_id = ".$equipoEvaluado['evaluado_id']." , equipo_id = ".$equipoEvaluado['equipo_id']." WHERE id = ".$equipoEvaluado["id"].";<br>";
                }
                $message .= "</h3></body></html>";
    
                Yii::$app->mailer->compose()
                      ->setTo(["samanriquej@grupokonecta.com","jonathan.arroyave@grupokonecta.com","anmorenoa@grupokonecta.com"])
                      ->setFrom(Yii::$app->params['email_satu_from'])
                      ->setSubject("BackUp Equipos")
                      ->setHtmlBody($message)
                      ->send();
            }


            /**
             * Creates a new Tipobloques model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Tipobloques();

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Updates an existing Tipobloques model.
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
             * Deletes an existing Tipobloques model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                $this->findModel($id)->delete();

                return $this->redirect(['index']);
            }

            /**
             * Finds the Tipobloques model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Tipobloques the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Tipobloques::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

        }