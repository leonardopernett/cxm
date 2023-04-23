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
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\helpers\Url;
use PHPExcel;
use PHPExcel_IOFactory;
use GuzzleHttp;
use app\models\Tmpejecucionformularios;
use app\models\Calificaciondetalles;


  class ApiprocesosmeliController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'verbs' => [          
          'class' => VerbFilter::className(),
          'actions' => [
            'delete' => ['post'],
          ],
        ],

        'access' => [
            'class' => AccessControl::classname(),
            'denyCallback' => function ($rule, $action) {
                    $msg = \Yii::t('app', 'The requested Item could not be found.');
                    Yii::$app->session->setFlash('danger', $msg);
                    $url = \yii\helpers\Url::to(['site/index']);
                    return $action->controller->redirect($url);
            },

            
            'rules' => [
              [
                'actions' => ['apivaloraciones','apiprocesosvalora','apiprocesosasesores'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apivaloraciones','apiprocesosvalora','apiprocesosasesores'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApivaloraciones(){
        $datapost = file_get_contents('php://input');
        $data_post = json_decode($datapost,true);

        ini_set("max_execution_time", "900");
        ini_set("memory_limit", "1024M");
        ini_set( 'post_max_size', '1024M' );

        ignore_user_abort(true);
        set_time_limit(900);

        $varProcesosBuscar = [':varCliente'=>"client_problem_rep"];

        $varListDataValoracion = Yii::$app->get('dbmeli')->createCommand('
            SELECT
                m.submission_id,
                m.cx_queue_name AS formulario,
                m.user_ldap AS valorado,
                m.user_team_leader_ldap AS lider,
                m.analysis_owner_ldap AS valorador,
                m.analysis_reason AS dimensiones,
                m.pc_comment_analysis AS comentarios,
                m.oe_extra_mile AS scoregeneral,
                m.action_datetime AS fechacreacion
 
            FROM meli_178619_NRT_KTA_OE_ACTION_POINTS_REASONS_V3 m
            WHERE 
                m.pc_name = :varCliente
                    AND m.oe_extra_mile IS NOT NULL 
                      AND m.action_datetime >= DATE_FORMAT(NOW() ,"%Y-%m-01")
            GROUP BY m.submission_id
              ORDER BY m.action_datetime
        ')->bindValues($varProcesosBuscar)->queryAll();

        foreach ($varListDataValoracion as $key => $value) {

          $varExisteConexion = (new \yii\db\Query())
                                ->select([
                                  'tbl_conexionvaloracion_datosorigen.identificador_origen'
                                ])
                                ->from(['tbl_conexionvaloracion_datosorigen'])
                                ->where(['=','tbl_conexionvaloracion_datosorigen.anulado',0])
                                ->andwhere(['=','tbl_conexionvaloracion_datosorigen.identificador_origen',$value['submission_id']])
                                ->count();

          if ($varExisteConexion == 0) {
            Yii::$app->db->createCommand()->insert('tbl_conexionvaloracion_datosorigen',[
                'identificador_origen' => $value['submission_id'],
                'formulario_origen' => $value['formulario'],
                'valorado_origen' => $value['valorado'],
                'lider_origen' => $value['lider'],
                'valorador_origen' => $value['valorador'],
                'dimensiones_origen' => $value['dimensiones'],
                'comentarios_origen' => $value['comentarios'],
                'score_origen' => $value['scoregeneral'],
                'fechacreacion_origen' => $value['fechacreacion'],
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => 1,
            ])->execute();

          }    

        }

        die();

    }

    public function actionApiprocesosasesores(){
      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);

      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varListadoProcesosAsesores = (new \yii\db\Query())
                            ->select([
                              'tbl_conexionvaloracion_datosorigen.valorado_origen'
                            ])
                            ->from(['tbl_conexionvaloracion_datosorigen'])
                            ->where(['=','tbl_conexionvaloracion_datosorigen.anulado',0])
                            ->groupby(['tbl_conexionvaloracion_datosorigen.valorado_origen'])
                            ->all();


      foreach ($varListadoProcesosAsesores as $key => $value) {
        $varOrigenAsesor = $value['valorado_origen'];

        $paramsBuscaValorado = [':ProcesosValorado'=>$varOrigenAsesor];
        $varValoradoJarvis = Yii::$app->dbjarvis->createCommand('
          SELECT dp_usuarios_actualizacion.documento FROM dp_usuarios_actualizacion
            WHERE 
              dp_usuarios_actualizacion.usuario = :ProcesosValorado')->bindValues($paramsBuscaValorado)->queryScalar();

        if ($varValoradoJarvis == "") {
          $varValoradoId = 0;
        }else{
          $varValoradoId = (new \yii\db\Query())
                                    ->select(['id'])
                                    ->from(['tbl_evaluados'])
                                    ->where(['=','tbl_evaluados.identificacion',$varValoradoJarvis])
                                    ->scalar();
        }

        $varVerificarExistente = (new \yii\db\Query())
                                    ->select(['id_datosasesores'])
                                    ->from(['tbl_conexionvaloracion_datosasesores'])
                                    ->where(['=','tbl_conexionvaloracion_datosasesores.asesormeli_origen',$varOrigenAsesor])
                                    ->count();

        if ($varVerificarExistente == 0) {
          Yii::$app->db->createCommand()->insert('tbl_conexionvaloracion_datosasesores',[
                'documento_origen' => $varValoradoJarvis,
                'asesorkonecta_origen' => $varValoradoId,
                'asesormeli_origen' => $varOrigenAsesor,
                'activo' => 1,
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => 1,
          ])->execute();
        }
        
      }

      die();
    }

    public function actionApiprocesosvalora(){
      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);

      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varFechaMesActual = date('Y-m-01');

      $count = 0;

      $varListadoProcesos = (new \yii\db\Query())
                            ->select([
                              '*'
                            ])
                            ->from(['tbl_conexionvaloracion_datosorigen'])
                            ->where(['=','tbl_conexionvaloracion_datosorigen.anulado',0])
                            ->andwhere(['=','tbl_conexionvaloracion_datosorigen.gestor_valora',2])
                            ->andwhere(['>=','tbl_conexionvaloracion_datosorigen.fechacreacion_origen',$varFechaMesActual])
                            ->all();


      foreach ($varListadoProcesos as $key => $value) {
        $varIdListado = $value['id_datosorigen'];
        $varIdFormularios = $value['formulario_origen'];
        $arrCheckPits = null;
        $arrFormulario = [];
        $arrayCountBloques = [];
        $arrayBloques = [];

        // Esta parte se obtiene la data para la tabla tbl_ejecucionformularios

        $varDimensionIdCxm = (new \yii\db\Query())
                          ->select([
                            'tbl_conexionvaloracion_datosdimension.dimension_id_cxm'
                          ])
                          ->from(['tbl_conexionvaloracion_datosdimension'])
                          ->where(['=','tbl_conexionvaloracion_datosdimension.anulado',0])
                          ->andwhere(['=','tbl_conexionvaloracion_datosdimension.dimensiones_origen',$value['dimensiones_origen']])
                          ->scalar();
        
        $varArbolIdCxm = (new \yii\db\Query())
                          ->select([
                            'tbl_valoracion_formulariosexcel.formulario_cxm'
                          ])
                          ->from(['tbl_valoracion_formulariosexcel'])
                          ->where(['=','tbl_valoracion_formulariosexcel.anulado',0])
                          ->andwhere(['=','tbl_valoracion_formulariosexcel.servicio_excel',$varIdFormularios])
                          ->scalar();

        $varValoradorIdCxm = (new \yii\db\Query())
                          ->select([
                            'tbl_usuarios.usua_id'
                          ])
                          ->from(['tbl_usuarios'])
                          ->join('LEFT OUTER JOIN', 'tbl_conexionvaloracion_datosusuarios',
                                      'tbl_usuarios.usua_identificacion = tbl_conexionvaloracion_datosusuarios.documento_origen')
                          ->where(['=','tbl_conexionvaloracion_datosusuarios.anulado',0])
                          ->andwhere(['=','tbl_conexionvaloracion_datosusuarios.usuariomeli_origen',$value['valorador_origen']])
                          ->scalar();

        $varAsesorIdCxm = (new \yii\db\Query())
                          ->select([
                            'tbl_conexionvaloracion_datosasesores.asesorkonecta_origen'
                          ])
                          ->from(['tbl_conexionvaloracion_datosasesores'])
                          ->where(['=','tbl_conexionvaloracion_datosasesores.anulado',0])
                          ->andwhere(['=','tbl_conexionvaloracion_datosasesores.asesormeli_origen',$value['valorado_origen']])
                          ->scalar();

        $varFormularioIdCxm = (new \yii\db\Query())
                          ->select([
                            'tbl_arbols.formulario_id'
                          ])
                          ->from(['tbl_arbols'])
                          ->where(['=','tbl_arbols.id',$varArbolIdCxm])
                          ->scalar();

        $varCreatedCxm = date('Y-m-d H:i:s', strtotime($value['fechacreacion_origen']));

        $vardsFuenteCxm = 'Proceso Externo '.$varIdListado." - ".$varCreatedCxm;

        $vardsComentariosCxm = $value['comentarios_origen'];

        $varArbolRutaCxm = (new \yii\db\Query())
                          ->select([
                            'tbl_arbols.dsname_full'
                          ])
                          ->from(['tbl_arbols'])
                          ->where(['=','tbl_arbols.id',$varArbolIdCxm])
                          ->scalar();

        $varLiderIdCxm = (new \yii\db\Query())
                          ->select([
                            'tbl_usuarios.usua_id'
                          ])
                          ->from(['tbl_usuarios'])
                          ->join('LEFT OUTER JOIN', 'tbl_conexionvaloracion_datosusuarios',
                                      'tbl_usuarios.usua_identificacion = tbl_conexionvaloracion_datosusuarios.documento_origen')
                          ->where(['=','tbl_conexionvaloracion_datosusuarios.anulado',0])
                          ->andwhere(['=','tbl_conexionvaloracion_datosusuarios.usuariomeli_origen',$value['lider_origen']])
                          ->scalar();

        $varEquipoId = (new \yii\db\Query())
                          ->select([
                            'tbl_equipos.id'
                          ])
                          ->from(['tbl_equipos'])
                          ->where(['=','tbl_equipos.usua_id',$varLiderIdCxm])
                          ->scalar();

        $varScore = $value['score_origen'];

        $varSesionCxm = (new \yii\db\Query())
                          ->select([
                            'tbl_seccions.id'
                          ])
                          ->from(['tbl_seccions'])
                          ->where(['=','tbl_seccions.formulario_id',$varFormularioIdCxm])
                          ->scalar();

        $varBloquesCxm = (new \yii\db\Query())
                          ->select([
                            'tbl_bloques.id'
                          ])
                          ->from(['tbl_bloques'])
                          ->where(['=','tbl_bloques.seccion_id',$varSesionCxm])
                          ->scalar();

        $varBloqueDetalleCxm = (new \yii\db\Query())
                                ->select([
                                  'tbl_bloquedetalles.calificacion_id'
                                ])
                                ->from(['tbl_bloquedetalles'])
                                ->where(['=','tbl_bloquedetalles.bloque_id',$varBloquesCxm])
                                ->scalar();

        $varCalificacionDetalleCxm = (new \yii\db\Query())
                                ->select([
                                  'tbl_calificaciondetalles.id'
                                ])
                                ->from(['tbl_calificaciondetalles'])
                                ->where(['=','tbl_calificaciondetalles.calificacion_id',$varBloqueDetalleCxm])
                                ->andwhere(['=','tbl_calificaciondetalles.name',$varScore])
                                ->scalar();

        $varCalificacionDetalleNameCxm = (new \yii\db\Query())
                                ->select([
                                  'tbl_calificaciondetalles.name'
                                ])
                                ->from(['tbl_calificaciondetalles'])
                                ->where(['=','tbl_calificaciondetalles.calificacion_id',$varBloqueDetalleCxm])
                                ->andwhere(['=','tbl_calificaciondetalles.name',$varScore])
                                ->scalar();

        $varSubirCalculoCxm = (new \yii\db\Query())
                                ->select([
                                  'tbl_formularios.subi_calculo'
                                ])
                                ->from(['tbl_formularios'])
                                ->where(['=','tbl_formularios.id',$varFormularioIdCxm])
                                ->scalar();

        // CONSULTO SI YA EXISTE LA EVALUACION
        $varCondicionalForm = [
          "usua_id" => $varValoradorIdCxm,
          "arbol_id" => $varArbolIdCxm,
          "evaluado_id" => $varAsesorIdCxm,
          "dimension_id" => $varDimensionIdCxm,
          "basesatisfaccion_id" => null,
          "dscomentario" => $varCreatedCxm,
        ];

        $idForm = \app\models\Ejecucionformularios::findOne($varCondicionalForm);

        if (empty($idForm)) {
          
          $varCondicional = [
            "usua_id" => $varValoradorIdCxm,
            "arbol_id" => $varArbolIdCxm,
            "evaluado_id" => $varAsesorIdCxm,
            "dimension_id" => $varDimensionIdCxm,
            "basesatisfaccion_id" => null,
            "sneditable" => 1,
          ];

          $idTmpForm = \app\models\Tmpejecucionformularios::findOne($varCondicional);

          if (empty($idTmpForm)) {
            $tmpeje = new \app\models\Tmpejecucionformularios();
            $tmpeje->dimension_id = $varDimensionIdCxm;
            $tmpeje->arbol_id = $varArbolIdCxm;
            $tmpeje->usua_id = $varValoradorIdCxm;
            $tmpeje->evaluado_id = $varAsesorIdCxm;
            $tmpeje->formulario_id = $varFormularioIdCxm;
            $tmpeje->created = $varCreatedCxm;
            $tmpeje->sneditable = 1;
            date_default_timezone_set('America/Bogota');
            $tmpeje->hora_inicial = date("Y-m-d H:i:s");

            $tmpeje->tipo_interaccion = 1;
            $tmpeje->save();
            $tmp_id = $tmpeje->id;        

            $varIDTmpBloquedetallesCalificacionCxm = (new \yii\db\Query())
                                  ->select([
                                    'tbl_tmpejecucionbloquedetalles.id'
                                  ])
                                  ->from(['tbl_tmpejecucionbloquedetalles'])
                                  ->where(['=','tbl_tmpejecucionbloquedetalles.tmpejecucionformulario_id',$tmp_id])
                                  ->scalar();

            $arrCalificaciones = array();
            $arrCalificaciones = [$varCalificacionDetalleCxm];

            $arrFormulario["equipo_id"] = $varEquipoId;
            $arrFormulario["usua_id_lider"] = $varLiderIdCxm;
            $arrFormulario["dimension_id"] = $varDimensionIdCxm;
            $arrFormulario["dsruta_arbol"] = $varArbolRutaCxm;
            $arrFormulario["dscomentario"] = $vardsComentariosCxm;
            $arrFormulario["dsfuente_encuesta"] = $vardsFuenteCxm;
            $arrFormulario["transacion_id"] = 1;
            $arrFormulario["sn_mostrarcalculo"] = 1;

            //  CONSULTA DEL FORMULARIO PARA VERIFICAR EL SUBIRCALCULO
            $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
            if (isset($varSubirCalculoCxm) AND $varSubirCalculoCxm != '') {
              $data->subi_calculo .= $varSubirCalculoCxm;
              $data->save();
            }

            // SE PROCEDE A ACTUALIZAR LA TEMPORAL
            $model = \app\models\Tmpejecucionformularios::find()->where(["id" => $tmp_id])->one();
            $model->usua_id_actual = $varValoradorIdCxm;               
            $model->save();
            

            //TO-DO  : COMENTAR LINEA EN CASO DE NO NECESITAR LO DE ADICIONAR Y ESCALAR
            /* Guardo en la tabla tbl_registro_ejec para tener un seguimiento 
            * de los diversos involucrados en la valoracion en el tiempo */
            $modelRegistro = \app\models\RegistroEjec::findOne(['ejec_form_id' => $model->ejecucionformulario_id, 'valorador_id' => $model->usua_id]);
                    
            if (!isset($modelRegistro)) {

              $modelRegistro = new \app\models\RegistroEjec();
              $modelRegistro->ejec_form_id = $tmp_id;
              $modelRegistro->descripcion = 'Primera valoración externa';
            }
                    
            $modelRegistro->dimension_id = $varDimensionIdCxm;
            $modelRegistro->valorado_id = $data->evaluado_id;
            $modelRegistro->valorador_id = $data->usua_id;
            $modelRegistro->pcrc_id = $data->arbol_id;
            $modelRegistro->tipo_interaccion = $data->tipo_interaccion;
            $modelRegistro->fecha_modificacion = date("Y-m-d H:i:s");
            
            $modelRegistro->save();

            \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);
            \app\models\Tmpejecucionsecciones::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
            \app\models\Tmpejecucionbloques::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);

            // SE GUARDAN LAS CALIFICACIONES
            foreach ($arrCalificaciones as $form_detalle_id => $calif_detalle_id) {
              $arrDetalleForm = [];

              //se valida que existan check de pits seleccionaddos y se valida
              //que exista el del bloquedetalle actual para actualizarlo
              if (count($arrCheckPits) > 0) {
                if (isset($arrCheckPits[$varIDTmpBloquedetallesCalificacionCxm])) {
                  $arrDetalleForm["c_pits"] = $arrCheckPits[$varIDTmpBloquedetallesCalificacionCxm];
                }
              }
              
              if (empty($calif_detalle_id)) {
                $arrDetalleForm["calificaciondetalle_id"] = -1;
              } else {
                $arrDetalleForm["calificaciondetalle_id"] = $calif_detalle_id;
              }

              
              \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ["id" => $varIDTmpBloquedetallesCalificacionCxm]);
              $calificacion = \app\models\Tmpejecucionbloquedetalles::findOne(["id" => $varIDTmpBloquedetallesCalificacionCxm]);
              

              // Cuento las preguntas en las cuales esta seleccionado el NA
              //lleno $arrayBloques para tener marcados en que bloques no se selecciono el check
              
              if (!in_array($varBloquesCxm, $arrayBloques) && (strtoupper($varCalificacionDetalleNameCxm) == 'NA')) {
                
                $arrayBloques[] = $varBloquesCxm;
                
                //inicio $arrayCountBloques
                $arrayCountBloques[$count] = [($varBloquesCxm) => 1];
                $count++;
                
              } else {
              
                //actualizo $arrayCountBloques sumandole 1 cada q encuentra un NA de ese bloque
                if (count($arrayCountBloques) != 0) {
                  if ((array_key_exists($calificacion->bloque_id, $arrayCountBloques[count($arrayCountBloques) - 1])) && (strtoupper($varCalificacionDetalleNameCxm) == 'NA')) {
                    
                    $arrayCountBloques[count($arrayCountBloques) - 1][$varBloquesCxm] = ($arrayCountBloques[count($arrayCountBloques) - 1][$cvarBloquesCxm] + 1);
                  
                  }
                }
              }
            }
            
            //Actualizo los bloques en los cuales el total de sus preguntas esten seleccionadas en NA
            foreach ($arrayCountBloques as $dato) {
              
              $totalPreguntasBloque = \app\models\Tmpejecucionbloquedetalles::find()->select("COUNT(id) as preguntas")->from("tbl_tmpejecucionbloquedetalles")->where(['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)])->asArray()->all();
              
              if ($dato[key($dato)] == $totalPreguntasBloque["0"]["preguntas"]) {
              
                \app\models\Tmpejecucionbloques::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)]);
                
              }
            }

            //actualizo las secciones, la cuales tienen todos sus bloques con la opcion snna en 1
            $secciones = \app\models\Tmpejecucionsecciones::findAll(['tmpejecucionformulario_id' => $tmp_id]);
            foreach ($secciones as $seccion) {
              $bloquessnna = \app\models\Tmpejecucionformularios::find()
                              ->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                              ->from("tbl_tmpejecucionformularios f")
                              ->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                              ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                              ->where(['b.snna' => 1, 's.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                              ->groupBy("s.id")->asArray()
                              ->all();

              $totalBloques = \app\models\Tmpejecucionformularios::find()
                              ->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                              ->from("tbl_tmpejecucionformularios f")
                              ->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                              ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                              ->where(['s.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                              ->groupBy("s.id")->asArray()
                              ->all();

              if (count($bloquessnna) > 0) {
                if ($bloquessnna[0]['conteo'] == $totalBloques[0]['conteo']) {
                
                  \app\models\Tmpejecucionsecciones::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'seccion_id' => ($seccion->seccion_id)]);
                  
                }
              }
            }

            /* GUARDAR EL TMP FOMULARIO A LAS EJECUCIONES */
            $validarPasoejecucionform = \app\models\Tmpejecucionformularios::guardarFormulario($tmp_id);

            if (!$validarPasoejecucionform) {
              Yii::$app->db->createCommand()->insert('tbl_conexionvaloracion_datosnovalorados',[
                'identificador_no_origen' => $value['submission_id'],
                'formulario_no_origen' => $value['formulario'],
                'valorado_no_origen' => $value['valorado'],
                'lider_no_origen' => $value['lider'],
                'valorador_no_origen' => $value['valorador'],
                'dimensiones_no_origen' => $value['dimensiones'],
                'comentarios_no_origen' => $value['comentarios'],
                'score_no_origen' => $value['scoregeneral'],
                'fechacreacion_no_origen' => $value['fechacreacion'],
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
                'usua_id' => 1,
              ])->execute();
            }

          }

        }
        
      }


      die();
    }

  }

?>
