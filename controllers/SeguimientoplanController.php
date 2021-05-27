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
use app\models\ControlProcesosPlan;
use app\models\PlanEscalamientos;
use app\models\PlanPermisos;
use PHPExcel;
use PHPExcel_IOFactory;


    class SeguimientoPlanController extends \yii\web\Controller {

        public function behaviors(){
            return[
                'access' => [
                        'class' => AccessControl::classname(),
                        'only' => ['index','view','escalamientos','viewgestion','exportarexcel','gestionarescalamientos','editarplan','graficasequipo','asignarpermisos','viewequipo','excelplanx','excelplanpast','administrar'],
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isHacerMonitoreo();
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
        
        public function actionIndex(){
            $model = new ControlProcesosPlan();
         
            $dataProvider = $model->buscarcontrolplan(Yii::$app->request->post());

            return $this->render('index',[
                'model' => $model,
                'dataProvider' => $dataProvider,
                ]);
        }

        public function actionView($id,$evaluados_id){
            $varId = $id;
            $varEvaluado = $evaluados_id;

            return $this->render('view',[
                'varId' => $varId,
                'varEvaluado' => $varEvaluado,
                ]);
        }

        public function actionEscalamientos($varid,$evaluados_id){
	    $model = new PlanEscalamientos();
            $varidevaluados = $evaluados_id;
            $varids = $varid;
           

            return $this->render('escalamientos',[
                'varidevaluados' => $varidevaluados,
                'varids' => $varids,
                'model' => $model,
                ]);
        }

        public function actionViewgestion($varid,$evaluados_id){
            $txtid = $varid;
            $txtevaluados_id = $evaluados_id;

            return $this->renderAjax('viewgestion',[
                'txtid' => $txtid,
                'txtevaluados_id' => $txtevaluados_id,
                ]);
        }

        public function actionExportarexcel($varid,$evaluados_id){
            $varId = $varid;
            $varEvaluado = $evaluados_id;

            return $this->renderAjax('exportarexcel',[
                'varId' => $varId,
                'varEvaluado' => $varEvaluado,
                ]);
        }

        public function actionViewhistorico(){
            $model = new ControlProcesosPlan();
            $varidtc = null;

            $formData = Yii::$app->request->post();
            if ($model->load($formData)) {
                $varidtc = $model->idtc;
            }

            return $this->render('viewhistorico',[
                'model' => $model,
                'varidtc' => $varidtc,
                ]);
        }

        public function actionGestionarescalamientos(){

            return $this->render('gestionarescalamientos');
        }

        public function actionEditarplan($varidplan,$varestado){

            Yii::$app->db->createCommand("update tbl_plan_escalamientos set Estado = $varestado where anulado = 0 and idplanjustificar = $varidplan")->execute();

            return $this->redirect('gestionarescalamientos');

        }

        public function actionGraficasequipo(){
            $month = date('m');
            $year = date('Y');
            $day = date("d", mktime(0,0,0, $month+1, 0, $year));
             
            $varfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
            $varfechafin = date('Y-m-d', mktime(0,0,0, $month, $day, $year));

            $sessiones1 = Yii::$app->user->identity->id;

            $rol =  new Query;
            $rol     ->select(['tbl_roles.role_id'])
                        ->from('tbl_roles')
                        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                    'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                    'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                        ->where('tbl_usuarios.usua_id = '.$sessiones1.'');                    
            $command = $rol->createCommand();
            $roles = $command->queryScalar();

            $varlistEscalamientos = null;
            if ($roles == '270') {
                $varlistEscalamientos = Yii::$app->db->createCommand("select distinct evaluados_id from tbl_control_procesos where anulado = 0 and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();
            }else{
                $vartipopermiso = Yii::$app->db->createCommand("select tipopermiso from tbl_plan_permisos where anulado = 0 and usuaidpermiso = $sesiones")->queryScalar();
                $vararbol = Yii::$app->db->createCommand("select arbol_id from tbl_plan_permisos where anulado = 0 and usuaidpermiso = $sesiones")->queryScalar();

                if ($vartipopermiso == 1) {
                    $varlistEscalamientos = Yii::$app->db->createCommand("select distinct cp1.evaluados_id from tbl_control_procesos cp1 inner join tbl_control_params cp2 on cp1.evaluados_id = cp2.evaluados_id   inner join tbl_arbols a on cp2.arbol_id = a.id where cp1.anulado = 0 and cp2.anulado = 0            and cp2.fechacreacion between '$varfechainicio' and '$varfechafin' and cp1.fechacreacion between '$varfechainicio' and '$varfechafin' and a.arbol_id = $vararbol")->queryAll();
                }else{
                    $varlistEscalamientos = Yii::$app->db->createCommand("select distinct cp1.evaluados_id from tbl_control_procesos cp1 inner join tbl_control_params cp2 on cp1.evaluados_id = cp2.evaluados_id   inner join tbl_arbols a on cp2.arbol_id = a.id where cp1.anulado = 0 and cp2.anulado = 0            and cp2.fechacreacion between '$varfechainicio' and '$varfechafin' and cp1.fechacreacion between '$varfechainicio' and '$varfechafin' and cp1.responsable = $sessiones1 and a.arbol_id = $vararbol")->queryAll();
                }
            }

            $vararrayusuarios = array();
            foreach ($varlistEscalamientos as $key => $value) {
                array_push($vararrayusuarios, $value['evaluados_id']);                
            }
            $varlistausuarios = implode(", ", $vararrayusuarios);


            $fechainiC = $varfechainicio.' 00:00:00';
            $fechafinC = $varfechafin.' 23:59:59';

            $querys =  new Query;
            $querys ->select(['date_format(tbl_ejecucionformularios.created, "%Y/%m/%d")  as fecha', 'count(tbl_ejecucionformularios.created) as total'])
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                    'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
                    ->andwhere( " tbl_usuarios.usua_id in ($varlistausuarios)")
                    ->groupBy('fecha');
            $command = $querys->createCommand();
            $data = $command->queryAll();


            return $this->renderAjax('graficasequipo',[
                'data' => $data,
                ]);
        }

        public function actionAsignarpermisos(){
            $model = new PlanPermisos();

            $formData = Yii::$app->request->post();
            if ($model->load($formData)) {
                $varusuario = $model->usuaidpermiso;
                $vararbol = $model->arbol_id;

                Yii::$app->db->createCommand()->insert('tbl_plan_permisos',[
                                                'usuaidpermiso' => $varusuario, 
                                                'tipopermiso' => 1,
                                                'arbol_id' => $vararbol,   
                                                'anulado' => 0,
                                                'fechacreacion' => date("Y-m-d"),
                                                'usua_id' =>Yii::$app->user->identity->id,
                                           ])->execute();

                return $this->redirect('index');
            }

            return $this->renderAjax('asignarpermisos',[
                'model' => $model,
                ]);
        }

        public function actionViewequipo(){
            $model = new ControlProcesosPlan();
            $varcordi = null;
            $varusuar = null;

            $formData = Yii::$app->request->post();
            if ($model->load($formData)) {
                $varcordi = $model->responsable;
                $varusuar = $model->evaluados_id;
            }

            return $this->render('viewequipo',[
                'model' => $model,
                'varcordi' => $varcordi,
                'varusuar' => $varusuar,
                ]);
        }

        public function actionExcelplanx(){
            $month = date('m');
            $year = date('Y');
            $day = date("d", mktime(0,0,0, $month+1, 0, $year));
                         
            $varfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
            $varfechafin = date('Y-m-d', mktime(0,0,0, $month, $day, $year));

	    $varlistaPlanx = Yii::$app->get('dbslave')->createCommand("select   uu.usua_identificacion 'IdResponsable', uu.usua_nombre 'Repsonsable', u.usua_identificacion 'IdTecnico/Lider', u.usua_nombre 'Tecnico/Lider', rr.role_nombre 'Rol',     sum(cp2.cant_valor) 'Meta', (select distinct count(1) as cantidad from tbl_ejecucionformularios aa where aa.created between concat(t.fechainiciotc,' 00:00:00') and concat(t.fechafintc,' 23:59:59') and aa.usua_id = cp1.evaluados_id) 'Realizadas', (select distinct sum(pe.cantidadjustificar) from tbl_plan_escalamientos pe where pe.tecnicolider = cp1.evaluados_id and pe.Estado = 1 and pe.fechacreacion between t.fechainiciotc and t.fechafintc) 'NovedadesAprobadas', cp1.tipo_corte 'TipoCorte', cp1.idtc 'idcorte', cp1.evaluados_id 'idevaluado', if (gc.idgrupocorte  = 4,   round(sum(cp2.cant_valor/3)), round(sum(cp2.cant_valor/4))) 'MetaCorte1', (select distinct count(1) as cantidad from tbl_ejecucionformularios aa where aa.created between concat(tcu.fechainiciotcs,' 00:00:00') and concat(tcu.fechafintcs,' 23:59:59') and aa.usua_id = cp1.evaluados_id) 'Realizadas_Corte1', if (        gc.idgrupocorte  = 4, round(sum(cp2.cant_valor/3)), round(sum(cp2.cant_valor/4)) ) 'MetaCorte2',(select distinct count(1) as cantidad from tbl_ejecucionformularios aa where aa.created between concat(tcd.fechainiciotcs,' 00:00:00') and concat(tcd.fechafintcs,' 23:59:59') and aa.usua_id = cp1.evaluados_id) 'Realizadas_Corte2', if ( gc.idgrupocorte  = 4, round(sum(cp2.cant_valor/3)),  round(sum(cp2.cant_valor/4))  ) 'MetaCorte3', (select distinct count(1) as cantidad from tbl_ejecucionformularios aa where aa.created between concat(tct.fechainiciotcs,' 00:00:00') and concat(tct.fechafintcs,' 23:59:59') and aa.usua_id = cp1.evaluados_id) 'Realizadas_Corte3',  if ( gc.idgrupocorte  = 4,   round(sum(cp2.cant_valor/3)),        round(sum(cp2.cant_valor/4))) 'MetaCorte4', (select distinct count(1) as cantidad from tbl_ejecucionformularios aa where aa.created between concat(tcc.fechainiciotcs,' 00:00:00') and concat(tcc.fechafintcs,' 23:59:59') and aa.usua_id = cp1.evaluados_id) 'Realizadas_Corte4', (select distinct sum(pe.cantidadjustificar) from tbl_plan_escalamientos pe where pe.tecnicolider = cp1.evaluados_id and pe.Estado = 1 and pe.fechacreacion between tcu.fechainiciotcs and tcu.fechafintcs) 'NovedadesAprobada_Corte1',  (select distinct sum(pe.cantidadjustificar) from tbl_plan_escalamientos pe where pe.tecnicolider = cp1.evaluados_id and pe.Estado = 1 and pe.fechacreacion between tcd.fechainiciotcs and tcd.fechafintcs) 'NovedadesAprobada_Corte2', (select distinct sum(pe.cantidadjustificar) from tbl_plan_escalamientos pe where pe.tecnicolider = cp1.evaluados_id and pe.Estado = 1 and pe.fechacreacion between tct.fechainiciotcs and tct.fechafintcs) 'NovedadesAprobada_Corte3',  (select distinct sum(pe.cantidadjustificar) from tbl_plan_escalamientos pe where pe.tecnicolider = cp1.evaluados_id and pe.Estado = 1 and pe.fechacreacion between tcc.fechainiciotcs and tcc.fechafintcs) 'NovedadesAprobada_Corte4'  from  tbl_control_procesos cp1   inner join tbl_control_params cp2 on cp1.evaluados_id = cp2.evaluados_id  inner join tbl_usuarios u on cp1.evaluados_id = u.usua_id inner join tbl_usuarios uu on cp1.responsable = uu.usua_id  inner join rel_usuarios_roles r on u.usua_id = r.rel_usua_id inner join tbl_roles rr on r.rel_role_id = rr.role_id      inner join tbl_tipocortes t on cp1.idtc = t.idtc  inner join tbl_grupo_cortes gc on t.idgrupocorte = gc.idgrupocorte inner join tbl_tipos_cortes tcu on t.idtc = tcu.idtc and tcu.cortetcs = 'Corte 1'        inner join tbl_tipos_cortes tcd on t.idtc = tcd.idtc and tcd.cortetcs = 'Corte 2'  inner join tbl_tipos_cortes tct on t.idtc = tct.idtc and tct.cortetcs = 'Corte 3' left join tbl_tipos_cortes tcc on t.idtc = tcc.idtc and tcc.cortetcs = 'Corte 4'  where    cp1.anulado = 0 and cp2.anulado = 0 
            and cp2.fechacreacion between '$varfechainicio' and '$varfechafin'  and cp1.fechacreacion between '$varfechainicio' and '$varfechafin'  group by  cp1.evaluados_id")->queryAll();


            // $varlistaPlanx = Yii::$app->db->createCommand("select (select usua_identificacion from tbl_usuarios where usua_id = cp1.responsable) 'IdResponsable', (select usua_nombre from tbl_usuarios where usua_id = cp1.responsable) 'Repsonsable', (select usua_identificacion from tbl_usuarios where usua_id = cp1.evaluados_id) 'IdTecnico/Lider', (select usua_nombre from tbl_usuarios where usua_id = cp1.evaluados_id) 'Tecnico/Lider', rr.role_nombre 'Rol', sum(cp2.cant_valor) 'Meta', (select  distinct count(ef.created) from tbl_ejecucionformularios ef inner join tbl_usuarios u on ef.usua_id = u.usua_id where ef.created between concat((select fechainiciotcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 1'),' 00:00:00') and concat((select fechafintcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 4'), ' 23:59:59') and u.usua_id = cp1.evaluados_id ) 'Realizadas', cp1.tipo_corte 'TipoCorte', cp1.idtc 'idcorte', cp1.evaluados_id 'idevaluado', (select  distinct count(ef.created) from tbl_ejecucionformularios ef inner join tbl_usuarios u on         ef.usua_id = u.usua_id where ef.created between concat((select fechainiciotcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 1'),' 00:00:00') and concat((select fechafintcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 1'), ' 23:59:59') and u.usua_id = cp1.evaluados_id ) 'Realizadas_Corte1', (select  distinct count(ef.created) from tbl_ejecucionformularios ef inner join tbl_usuarios u on ef.usua_id = u.usua_id where ef.created between concat((select fechainiciotcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 2'),' 00:00:00') and concat((select fechafintcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 2'), ' 23:59:59') and u.usua_id = cp1.evaluados_id ) 'Realizadas_Corte2', (select  distinct count(ef.created) from tbl_ejecucionformularios ef inner join tbl_usuarios u on ef.usua_id = u.usua_id where ef.created between concat((select fechainiciotcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 3'),' 00:00:00') and concat((select fechafintcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 3'), ' 23:59:59') and u.usua_id = cp1.evaluados_id ) 'Realizadas_Corte3', (select  distinct count(ef.created) from tbl_ejecucionformularios ef inner join tbl_usuarios u on ef.usua_id = u.usua_id where     ef.created between concat((select fechainiciotcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 4'),' 00:00:00') and concat((select fechafintcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 4'), ' 23:59:59') and u.usua_id = cp1.evaluados_id ) 'Realizadas_Corte4' from  tbl_control_procesos cp1 inner join tbl_control_params cp2 on cp1.evaluados_id = cp2.evaluados_id inner join tbl_usuarios u on cp1.evaluados_id = u.usua_id inner join rel_usuarios_roles r on u.usua_id = r.rel_usua_id inner join tbl_roles rr on r.rel_role_id = rr.role_id inner join rel_grupos_usuarios gu on cp2.evaluados_id = gu.usuario_id inner join tbl_grupos_usuarios g on gu.grupo_id = g.grupos_id where cp1.anulado = 0 and cp1.anulado = 0 and cp2.fechacreacion between '$varfechainicio' and '$varfechafin' and cp1.fechacreacion between '$varfechainicio' and '$varfechafin' group by  cp1.evaluados_id")->queryAll();
            
            return $this->renderAjax('excelplanx',[
                'varlistaPlanx' => $varlistaPlanx,
                'varfechainicio' => $varfechainicio,
                'varfechafin' => $varfechafin,
                ]);
        }

        public function actionExcelplanpast(){
            $month = date('m') - 1;
            $year = date('Y');
            $day = date("d", mktime(0,0,0, $month+1, 0, $year));
                         
            $varfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
            $varfechafin = date('Y-m-d', mktime(0,0,0, $month, $day, $year));

            $varlistaPlany = Yii::$app->get('dbslave')->createCommand("select   uu.usua_identificacion 'IdResponsable', uu.usua_nombre 'Repsonsable', u.usua_identificacion 'IdTecnico/Lider', u.usua_nombre 'Tecnico/Lider', rr.role_nombre 'Rol',     sum(cp2.cant_valor) 'Meta', (select distinct count(1) as cantidad from tbl_ejecucionformularios aa where aa.created between concat(t.fechainiciotc,' 00:00:00') and concat(t.fechafintc,' 23:59:59') and aa.usua_id = cp1.evaluados_id) 'Realizadas', (select distinct sum(pe.cantidadjustificar) from tbl_plan_escalamientos pe where pe.tecnicolider = cp1.evaluados_id and pe.Estado = 1 and pe.fechacreacion between t.fechainiciotc and t.fechafintc) 'NovedadesAprobadas', cp1.tipo_corte 'TipoCorte', cp1.idtc 'idcorte', cp1.evaluados_id 'idevaluado', if (gc.idgrupocorte  = 4,   round(sum(cp2.cant_valor/3)), round(sum(cp2.cant_valor/4))) 'MetaCorte1', (select distinct count(1) as cantidad from tbl_ejecucionformularios aa where aa.created between concat(tcu.fechainiciotcs,' 00:00:00') and concat(tcu.fechafintcs,' 23:59:59') and aa.usua_id = cp1.evaluados_id) 'Realizadas_Corte1', if (        gc.idgrupocorte  = 4, round(sum(cp2.cant_valor/3)), round(sum(cp2.cant_valor/4)) ) 'MetaCorte2',(select distinct count(1) as cantidad from tbl_ejecucionformularios aa where aa.created between concat(tcd.fechainiciotcs,' 00:00:00') and concat(tcd.fechafintcs,' 23:59:59') and aa.usua_id = cp1.evaluados_id) 'Realizadas_Corte2', if ( gc.idgrupocorte  = 4, round(sum(cp2.cant_valor/3)),  round(sum(cp2.cant_valor/4))  ) 'MetaCorte3', (select distinct count(1) as cantidad from tbl_ejecucionformularios aa where aa.created between concat(tct.fechainiciotcs,' 00:00:00') and concat(tct.fechafintcs,' 23:59:59') and aa.usua_id = cp1.evaluados_id) 'Realizadas_Corte3',  if ( gc.idgrupocorte  = 4,   round(sum(cp2.cant_valor/3)),        round(sum(cp2.cant_valor/4))) 'MetaCorte4', (select distinct count(1) as cantidad from tbl_ejecucionformularios aa where aa.created between concat(tcc.fechainiciotcs,' 00:00:00') and concat(tcc.fechafintcs,' 23:59:59') and aa.usua_id = cp1.evaluados_id) 'Realizadas_Corte4', (select distinct sum(pe.cantidadjustificar) from tbl_plan_escalamientos pe where pe.tecnicolider = cp1.evaluados_id and pe.Estado = 1 and pe.fechacreacion between tcu.fechainiciotcs and tcu.fechafintcs) 'NovedadesAprobada_Corte1',  (select distinct sum(pe.cantidadjustificar) from tbl_plan_escalamientos pe where pe.tecnicolider = cp1.evaluados_id and pe.Estado = 1 and pe.fechacreacion between tcd.fechainiciotcs and tcd.fechafintcs) 'NovedadesAprobada_Corte2', (select distinct sum(pe.cantidadjustificar) from tbl_plan_escalamientos pe where pe.tecnicolider = cp1.evaluados_id and pe.Estado = 1 and pe.fechacreacion between tct.fechainiciotcs and tct.fechafintcs) 'NovedadesAprobada_Corte3',  (select distinct sum(pe.cantidadjustificar) from tbl_plan_escalamientos pe where pe.tecnicolider = cp1.evaluados_id and pe.Estado = 1 and pe.fechacreacion between tcc.fechainiciotcs and tcc.fechafintcs) 'NovedadesAprobada_Corte4'  from  tbl_control_procesos cp1   inner join tbl_control_params cp2 on cp1.evaluados_id = cp2.evaluados_id  inner join tbl_usuarios u on cp1.evaluados_id = u.usua_id inner join tbl_usuarios uu on cp1.responsable = uu.usua_id  inner join rel_usuarios_roles r on u.usua_id = r.rel_usua_id inner join tbl_roles rr on r.rel_role_id = rr.role_id      inner join tbl_tipocortes t on cp1.idtc = t.idtc  inner join tbl_grupo_cortes gc on t.idgrupocorte = gc.idgrupocorte inner join tbl_tipos_cortes tcu on t.idtc = tcu.idtc and tcu.cortetcs = 'Corte 1'        inner join tbl_tipos_cortes tcd on t.idtc = tcd.idtc and tcd.cortetcs = 'Corte 2'  inner join tbl_tipos_cortes tct on t.idtc = tct.idtc and tct.cortetcs = 'Corte 3' left join tbl_tipos_cortes tcc on t.idtc = tcc.idtc and tcc.cortetcs = 'Corte 4'  where    cp1.anulado = 0 and cp2.anulado = 0 
            and cp2.fechacreacion between '$varfechainicio' and '$varfechafin'  and cp1.fechacreacion between '$varfechainicio' and '$varfechafin'  group by  cp1.evaluados_id")->queryAll();


            // $varlistaPlany = Yii::$app->db->createCommand("select (select usua_identificacion from tbl_usuarios where usua_id = cp1.responsable) 'IdResponsable', (select usua_nombre from tbl_usuarios where usua_id = cp1.responsable) 'Repsonsable', (select usua_identificacion from tbl_usuarios where usua_id = cp1.evaluados_id) 'IdTecnico/Lider', (select usua_nombre from tbl_usuarios where usua_id = cp1.evaluados_id) 'Tecnico/Lider', rr.role_nombre 'Rol', sum(cp2.cant_valor) 'Meta', (select  distinct count(ef.created) from tbl_ejecucionformularios ef inner join tbl_usuarios u on ef.usua_id = u.usua_id where ef.created between concat((select fechainiciotcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 1'),' 00:00:00') and concat((select fechafintcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 4'), ' 23:59:59') and u.usua_id = cp1.evaluados_id ) 'Realizadas', cp1.tipo_corte 'TipoCorte', cp1.idtc 'idcorte', cp1.evaluados_id 'idevaluado', (select  distinct count(ef.created) from tbl_ejecucionformularios ef inner join tbl_usuarios u on         ef.usua_id = u.usua_id where ef.created between concat((select fechainiciotcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 1'),' 00:00:00') and concat((select fechafintcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 1'), ' 23:59:59') and u.usua_id = cp1.evaluados_id ) 'Realizadas_Corte1', (select  distinct count(ef.created) from tbl_ejecucionformularios ef inner join tbl_usuarios u on ef.usua_id = u.usua_id where ef.created between concat((select fechainiciotcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 2'),' 00:00:00') and concat((select fechafintcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 2'), ' 23:59:59') and u.usua_id = cp1.evaluados_id ) 'Realizadas_Corte2', (select  distinct count(ef.created) from tbl_ejecucionformularios ef inner join tbl_usuarios u on ef.usua_id = u.usua_id where ef.created between concat((select fechainiciotcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 3'),' 00:00:00') and concat((select fechafintcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 3'), ' 23:59:59') and u.usua_id = cp1.evaluados_id ) 'Realizadas_Corte3', (select  distinct count(ef.created) from tbl_ejecucionformularios ef inner join tbl_usuarios u on ef.usua_id = u.usua_id where     ef.created between concat((select fechainiciotcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 4'),' 00:00:00') and concat((select fechafintcs from tbl_tipos_cortes where  idtc = cp1.idtc and cortetcs like 'Corte 4'), ' 23:59:59') and u.usua_id = cp1.evaluados_id ) 'Realizadas_Corte4' from  tbl_control_procesos cp1 inner join tbl_control_params cp2 on cp1.evaluados_id = cp2.evaluados_id inner join tbl_usuarios u on cp1.evaluados_id = u.usua_id inner join rel_usuarios_roles r on u.usua_id = r.rel_usua_id inner join tbl_roles rr on r.rel_role_id = rr.role_id inner join rel_grupos_usuarios gu on cp2.evaluados_id = gu.usuario_id inner join tbl_grupos_usuarios g on gu.grupo_id = g.grupos_id where cp1.anulado = 0 and cp1.anulado = 0 and cp2.fechacreacion between '$varfechainicio' and '$varfechafin' and cp1.fechacreacion between '$varfechainicio' and '$varfechafin' group by  cp1.evaluados_id")->queryAll();

            return $this->renderAjax('excelplanpast',[
                'varlistaPlany' => $varlistaPlany,
                'varfechainicio' => $varfechainicio,
                'varfechafin' => $varfechafin,
                ]);
        }

        public function actionAdministrar(){
            $model = new ControlProcesosPlan();
            $varlistadimension = null;
            $varusuaid = null;
            $varcorte = null;
            $varidtc = null;

            $formData = Yii::$app->request->post();
            if ($model->load($formData)) {
                $varusuaid = $model->evaluados_id;
                $varidtc = $model->idtc;
                $varcorte = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where idtc = $varidtc ")->queryScalar();

                $varfechacreacion = Yii::$app->db->createCommand("select distinct fechacreacion from tbl_control_procesos where evaluados_id = $varusuaid and idtc = $varidtc ")->queryScalar();

                $varlistadimension = Yii::$app->db->createCommand("select * from tbl_control_params where evaluados_id = $varusuaid and fechacreacion = '$varfechacreacion'")->queryAll();
            }

            return $this->render('administrar',[
                'model' => $model,
                'varlistadimension' => $varlistadimension,
                'varusuaid' => $varusuaid,
                'varcorte' => $varcorte,
                'varidtc' => $varidtc,
                ]);
        }

        public function actionVerificarcantidad(){
            $txtvaridarbols = Yii::$app->request->get("txtvaridarbols");
            $txtvartxtcantidad = Yii::$app->request->get("txtvartxtcantidad");
            $txtvaridtcgeneral = Yii::$app->request->get("txtvaridtcgeneral");
            $txtvaridusua = Yii::$app->request->get("txtvaridusua");
            $txtvaridtcs = Yii::$app->request->get("txtvaridtcs");

            $vartipogrupo = Yii::$app->get('dbslave')->createCommand("select tbl_grupo_cortes.idgrupocorte from tbl_grupo_cortes inner join tbl_tipocortes on  tbl_grupo_cortes.idgrupocorte = tbl_tipocortes.idgrupocorte inner join tbl_control_procesos on tbl_tipocortes.idtc = tbl_control_procesos.idtc where tbl_control_procesos.id = $txtvaridtcgeneral group by tbl_grupo_cortes.idgrupocorte")->queryScalar();

            $varfecha = Yii::$app->get('dbslave')->createCommand("select fechacreacion from tbl_control_procesos where id = $txtvaridtcgeneral")->queryScalar();

            $varcantidadarbol = Yii::$app->get('dbslave')->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id = $txtvaridusua and arbol_id = $txtvaridarbols and fechacreacion = '$varfecha'")->queryScalar();

            $varcantidadarbol2 = 0;
            if ($vartipogrupo == 4) {
                $varcantidadarbol2 = round($varcantidadarbol / 3);
            }else{
                $varcantidadarbol2 = round($varcantidadarbol / 4);
            }
            
            // var_dump($varcantidadarbol);
            
            if ($varcantidadarbol2 > $txtvartxtcantidad) {
                $varvalida = Yii::$app->db->createCommand("select sum(cantidadjustificar) from tbl_plan_escalamientos where tecnicolider = $txtvaridusua and arbol_id = $txtvaridarbols and idtcs = $txtvaridtcs and anulado = 0")->queryScalar();
                $varvalida = $varvalida + $txtvartxtcantidad;

                if ($varcantidadarbol2 > $varvalida) {
                    $txtrta = 0;
                }else{
                    $txtrta = 2;
                }   
            }else{
                $txtrta = 1;
            }

            die(json_encode($txtrta));
        }

        public function actionGuardarjustificacion(){
            $txtvaridtcs = Yii::$app->request->get("txtvaridtcs");
            $txtvaridarbols = Yii::$app->request->get("txtvaridarbols");
            $txtvarid_argumentos = Yii::$app->request->get("txtvarid_argumentos");
            $txtvartxtcantidad = Yii::$app->request->get("txtvartxtcantidad");
            $txtvartxtcorreoid = Yii::$app->request->get("txtvartxtcorreoid");
            $txtvaridtcgeneral = Yii::$app->request->get("txtvaridtcgeneral");
            $txtvaridusua = Yii::$app->request->get("txtvaridusua");
            $txtvarselect2chosen1 = Yii::$app->request->get("txtvarselect2chosen1");
            $txtvartxtcomentariosid = Yii::$app->request->get("txtvartxtcomentariosid");

            
            Yii::$app->db->createCommand()->insert('tbl_plan_escalamientos',[
                                'idtcs' => $txtvaridtcs,
                                'justificacion' => $txtvarid_argumentos,
                                'correo' => $txtvartxtcorreoid,
                                'comentarios' => $txtvartxtcomentariosid,
                                'Estado' => 0,
                                'tecnicolider' => $txtvaridusua,
                                'cantidadjustificar' => $txtvartxtcantidad,
                                'anulado' => 0,
                                'usua_id' => Yii::$app->user->identity->id,
                                'fechacreacion' => date("Y-m-d"),
                                'asesorid' => $txtvarselect2chosen1,
                                'arbol_id' => $txtvaridarbols,
                    ])->execute(); 


                // $phpExc = new \PHPExcel();
                // $phpExc->getProperties()
                //             ->setCreator("Konecta")
                //             ->setLastModifiedBy("Konecta")
                //             ->setTitle("Justificacion de Rendimiento - Valoraciones QA")
                //             ->setSubject("Justificacion de Rendimiento - Valoraciones QA")
                //             ->setDescription("Este archivo genera la justificacion del rednimiento de las valoraciones de un tecnico/lider.")
                //             ->setKeywords("Justificacion de Rendimiento - Valoraciones QA");
                // $phpExc->setActiveSheetIndex(0);

                // $phpExc->getActiveSheet()->setShowGridlines(False);

                // $styleArray = array(
                //     'alignment' => array(
                //         'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                //     ),
                // );

                // $styleArraySize = array(
                //     'font' => array(
                //             'bold' => true,
                //             'size'  => 15,
                //     ),
                //     'alignment' => array(
                //             'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                //     ), 
                // );

                // $styleColor = array( 
                //     'fill' => array( 
                //         'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                //         'color' => array('rgb' => '28559B'),
                //     )
                // );

                // $styleArrayTitle = array(
                //     'font' => array(
                //       'bold' => false,
                //       'color' => array('rgb' => 'FFFFFF')
                //     )
                // );

                // $styleArraySubTitle = array(              
                //     'fill' => array( 
                //             'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                //             'color' => array('rgb' => '4298B5'),
                //     )
                // );

                // $styleArrayBody = array(
                //     'font' => array(
                //         'bold' => false,
                //         'color' => array('rgb' => '2F4F4F')
                //     ),
                //     'borders' => array(
                //         'allborders' => array(
                //             'style' => \PHPExcel_Style_Border::BORDER_THIN,
                //             'color' => array('rgb' => 'DDDDDD')
                //         )
                //     )
                // );

                // $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);
                // $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
                // $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                // $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
                // $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
                // $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
                // $phpExc->setActiveSheetIndex(0)->mergeCells('A1:C1');

                //  $hoy = getdate();
                // $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday'];
              
                // $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                        
                // $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
                // $tmpFile.= ".xls";

                // $objWriter->save($tmpFile);

                // $message = "<html><body>";
                // $message .= "<h3>Se ha realizado el envio correcto de las justificaciones.</h3>";
                // $message .= "</body></html>";

                // Yii::$app->mailer->compose()
                //                 ->setTo($txtcorreo)
                //                 ->setFrom(Yii::$app->params['email_satu_from'])
                //                 ->setSubject("Envio Proceso de Escalamientos plan de valoracion")
                //                 ->attach($tmpFile)
                //                 ->setHtmlBody($message)
                //                 ->send();           
            $txtrta2 = 1;

            die(json_encode($txtrta2));
        }

        public function actionNegargestion($varidplan,$varestado){
            $model = new PlanEscalamientos();
            $txtvaridplan = $varidplan;
            $txtvarestado = $varestado;

            $formData = Yii::$app->request->post();
            if ($model->load($formData)) {
                $txtnegar = $model->negargestion;

                Yii::$app->db->createCommand("update tbl_plan_escalamientos set negargestion = '$txtnegar' where anulado = 0 and idplanjustificar = $txtvaridplan")->execute();

                Yii::$app->db->createCommand("update tbl_plan_escalamientos set Estado = $txtvarestado where anulado = 0 and idplanjustificar = $txtvaridplan")->execute();

                return $this->redirect('gestionarescalamientos');
                
            }

            return $this->render('negargestion',[
                'model' => $model,
                'txtvaridplan' => $txtvaridplan,
                ]);
        }




    }

?>