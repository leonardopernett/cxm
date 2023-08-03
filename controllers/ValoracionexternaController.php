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
use yii\db\mssql\PDO;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\helpers\Url;
use PHPExcel;
use PHPExcel_IOFactory;
use app\models\FormUploadtigo;
use app\models\UploadForm2;
use app\models\UploadForm3;
use app\models\Valoracionclientenuevo;
use app\models\Valoraciondatogeneral;
use app\models\Valoracionatributos;
use app\models\Valoraciondatoespecial;
use app\models\Valoracionformulariosexcel;
use app\models\ProcesosClienteCentrocosto;
use app\models\Arboles;
use GuzzleHttp;
use app\models\Tmpejecucionformularios;
use app\models\Calificaciondetalles;
use Exception;

  class ValoracionexternaController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index'],
              'rules' => [
                [
                  'allow' => true,
                  'roles' => ['@'],
                  'matchCallback' => function() {
                              return Yii::$app->user->identity->isAdminSistema() ||  Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo();
                          },
                ],
              ]
            ],
          'verbs' => [          
            'class' => VerbFilter::className(),
            'actions' => [
              'delete' => ['get'],
            ],
          ],

          'corsFilter' => [
            'class' => \yii\filters\Cors::class,
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
   
    public function actionIndex(){  

      $varListaGeneral = (new \yii\db\Query())
                                ->select([
                                  'tbl_valoracion_clientenuevo.id_clientenuevo',
                                  'tbl_arbols.name', 
                                  'tbl_hojavida_sociedad.sociedad',
                                  'tbl_valoracion_clientenuevo.id_dp_clientes'
                                ])
                                ->from(['tbl_valoracion_clientenuevo'])
                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_valoracion_clientenuevo.id_dp_clientes')
                                ->join('LEFT OUTER JOIN', 'tbl_hojavida_sociedad',
                                  'tbl_hojavida_sociedad.id_sociedad = tbl_valoracion_clientenuevo.id_sociedad')
                                ->where(['=','tbl_valoracion_clientenuevo.anulado',0])
                                ->groupby(['tbl_arbols.id'])
                                ->all();  

    

      return $this->render('index',[
        'varListaGeneral' => $varListaGeneral,
      ]);
    }


    public function actionAgregarpcrc($id_dp_clientes){

      $model = new Valoracionclientenuevo();

      $varData = (new \yii\db\Query())
                  ->select(['tbl_arbols.dsname_full','tbl_valoracion_clientenuevo.cod_pcrc','tbl_valoracion_clientenuevo.id_dp_clientes'])
                  ->from(['tbl_valoracion_clientenuevo'])
                  ->join('INNER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_valoracion_clientenuevo.cod_pcrc')
                  ->where(['=','id_dp_clientes',$id_dp_clientes])
                  ->all();

      $form = Yii::$app->request->post();
        if ($model->load($form) ) { 
        
          $cod_pcrc= $model->cod_pcrc;
          Yii::$app->db->createCommand()->insert('tbl_valoracion_clientenuevo',[
                      'id_dp_clientes' => $id_dp_clientes,
                      'cod_pcrc' => $cod_pcrc,
                      'anulado' => 0,
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
          ])->execute();
        return $this->redirect('index');
        }

      return $this->renderAjax('agregarpcrc',[
        'model' => $model,
        'varData' => $varData,
        'id_dp_clientes' => $id_dp_clientes
      ]);
    }

   public function actionSubircarga($cod_pcrc,$id_dp_clientes){
    $model = new FormUploadtigo();


    $varPreguntas = (new \yii\db\Query())
                        ->select(['tbl_bloquedetalles.name as nameP'])
                        ->from(['tbl_arbols'])
                        ->join('INNER JOIN','tbl_seccions',
                                        'tbl_seccions.formulario_id = tbl_arbols.formulario_id')
                        ->join('INNER JOIN','tbl_bloques',
                                        'tbl_bloques.seccion_id = tbl_seccions.id')
                        ->join('INNER JOIN','tbl_bloquedetalles',
                                        'tbl_bloquedetalles.bloque_id = tbl_bloques.id')
                        ->where(['=','tbl_arbols.id',$cod_pcrc])
                        ->all();


    $varNombreArbol = (new \yii\db\Query())
                        ->select(['tbl_arbols.name'])
                        ->from(['tbl_arbols'])
                        ->where(['=','tbl_arbols.id',$cod_pcrc])
                        ->scalar();
            
              
            if ($model->load(Yii::$app->request->post()))
            {
                $model->file = UploadedFile::getInstances($model, 'file');

                if ($model->file && $model->validate()) {
                    foreach ($model->file as $file) {
                        $fecha = date('Y-m-d-h-i-s');
                        $user = Yii::$app->user->identity->username;
                        $name = $fecha . '-' . $user . '-Meli';
                        $file->saveAs('categorias/' . $name . '.' . $file->extension);
                        $this->Importexcelvaloracion($name,$id_dp_clientes,$cod_pcrc);

                       

                        // return $this->redirect('viewusuariosencuestas');
                    }
                }
           }
      

      return $this->render('subircarga',[
        'model' => $model,
        'varPreguntas' => $varPreguntas,
        'varNombreArbol' => $varNombreArbol,
      ]);
    

   }

   public function Importexcelvaloracion($name,$id_dp_clientes,$cod_pcrc){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $inputFile = 'categorias/' . $name . '.xlsx';      

      try {
          $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
          $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
          $objPHPExcel = $objReader->load($inputFile);
      } catch (Exception $e) {
          die('Error');
      }

      $sheet = $objPHPExcel->getSheet(0);
      $highestRow = $sheet->getHighestRow();


      for ($i=3; $i <= $highestRow; $i++) { 



        $varAsesor = (new \yii\db\Query())
                        ->select(['tbl_evaluados.id'])
                        ->from(['tbl_evaluados'])
                        ->where(['LIKE','tbl_evaluados.name','%' . trim($sheet->getCell("A".$i)->getValue()) . '%',false])
                        ->scalar();

        $varValorador = (new \yii\db\Query())
                        ->select(['tbl_usuarios.usua_id'])
                        ->from(['tbl_usuarios'])
                        ->where(['LIKE','tbl_usuarios.usua_nombre','%' . trim($sheet->getCell("B".$i)->getValue()) . '%',false])
                        ->scalar();

        $varDimension = (new \yii\db\Query())
                        ->select(['tbl_dimensions.id'])
                        ->from(['tbl_dimensions'])
                        ->where(['LIKE','tbl_dimensions.name','%' . trim($sheet->getCell("C".$i)->getValue()) . '%',false])
                        ->scalar();

        $varFuente  = (new \yii\db\Query())
                        ->select(['tbl_etapa_cad.id_etapacad'])
                        ->from(['tbl_etapa_cad'])
                        ->where(['LIKE','tbl_etapa_cad.nombre','%' . trim($sheet->getCell("D".$i)->getValue()) . '%',false])
                        ->groupBy(['tbl_etapa_cad.id_etapacad'])
                        ->scalar();

        $varForm  = $cod_pcrc;// formulario 


        $varCreatedCxm = date('Y-m-d H:i:s');

        $varComentarios = trim($sheet->getCell("F".$i)->getValue());
        $varScore = trim($sheet->getCell("E".$i)->getValue());
        $varFechaMesActual = date('Y-m-01');


        $varArbolRutaCxm = (new \yii\db\Query())
                          ->select([
                                'tbl_arbols.dsname_full'
                          ])
                          ->from(['tbl_arbols'])
                          ->where(['=','tbl_arbols.id',$id_dp_clientes])
                          ->scalar();

        $varSesionCxm = (new \yii\db\Query())
                          ->select([
                                  'tbl_seccions.id','tbl_seccions.name as Nombreseccion'
                                ])
                          ->from(['tbl_seccions'])
                          ->where(['=','tbl_seccions.id',$varForm])
                          ->scalar();

        $varBloquesCxm = (new \yii\db\Query())
                          ->select([
                            'tbl_bloques.id','tbl_bloques.name as NombreBloque'
                          ])
                          ->from(['tbl_bloques'])
                          ->where(['=','tbl_bloques.id',$varSesionCxm])
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
                          ->where(['=','tbl_formularios.id',$varForm])
                          ->scalar();

        $varCondicionalForm = [
                                "usua_id" => $varValorador,
                                "arbol_id" => $id_dp_clientes,
                                "evaluado_id" => $varAsesor,
                                "dimension_id" => $varDimension,
                                "basesatisfaccion_id" => null,
                                ];
       
        if ($varAsesor > 0) {
        
          if ($varAsesor != "") {
                                    
                                    // CONSULTO SI YA EXISTE LA EVALUACION
            $varCondicionalForm = [
                                      "usua_id" => $varValorador,
                                      "arbol_id" => $id_dp_clientes,
                                      "evaluado_id" => $varAsesor,
                                      "dimension_id" => $varDimension,
                                      "basesatisfaccion_id" => null,
                                      "dscomentario" => $varCreatedCxm,
                                    ];
                        
                                    $idForm = \app\models\Ejecucionformularios::findOne($varCondicionalForm);
                        
              if (empty($idForm)) {
                                      
                                      $varCondicional = [
                                        "usua_id" => $varValorador,
                                        "arbol_id" => $id_dp_clientes,
                                        "evaluado_id" => $varAsesor,
                                        "dimension_id" => $varDimension,
                                        "basesatisfaccion_id" => null,
                                        "sneditable" => 1,
                                      ];
                        
                                      $idTmpForm = \app\models\Tmpejecucionformularios::findOne($varCondicional);
                        
                if (empty($idTmpForm)) {
                  $tmpeje = new \app\models\Tmpejecucionformularios();
                  $tmpeje->dimension_id = $varDimension;
                  $tmpeje->arbol_id = $id_dp_clientes;
                  $tmpeje->usua_id = $varValoradorIdCxm;
                  $tmpeje->evaluado_id = $varAsesor;
                  $tmpeje->formulario_id = $varForm;
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
                        
                  //$arrFormulario["equipo_id"] = $varEquipoId;
                  //$arrFormulario["usua_id_lider"] = $varLiderIdCxm;
                  $arrFormulario["dimension_id"] = $varDimension;
                  $arrFormulario["dsruta_arbol"] = $varArbolRutaCxm;
                  $arrFormulario["dscomentario"] = $varComentarios;
                  $arrFormulario["dsfuente_encuesta"] = $varFuente;
                  $arrFormulario["transacion_id"] = 1;
                  $arrFormulario["sn_mostrarcalculo"] = 1;
                        
                                        //  CONSULTA DEL FORMULARIO PARA VERIFICAR EL SUBIRCALCULO
                  $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                  if (isset($varSubirCalculoCxm) && $varSubirCalculoCxm != '') {
                    $data->subi_calculo .= $varSubirCalculoCxm;
                    $data->save();
                  }
                        
                  // SE PROCEDE A ACTUALIZAR LA TEMPORAL
                  $model = \app\models\Tmpejecucionformularios::find()->where(["id" => $tmp_id])->one();
                  $model->usua_id_actual = $varValorador;               
                  $model->save();
                                        
                        
                                        
                  \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);
                  \app\models\Tmpejecucionsecciones::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                  \app\models\Tmpejecucionbloques::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                        
                  // SE GUARDAN LAS CALIFICACIONES
                  foreach ($arrCalificaciones as $calif_detalle_id) {
                    $arrDetalleForm = [];
                        
                                          //se valida que existan check de pits seleccionaddos y se valida
                                          //que exista el del bloquedetalle actual para actualizarlo
                    if (count($arrCheckPits) > 0) {
                      if (isset($arrCheckPits[$varIDTmpBloquedetallesCalificacionCxm])) {
                        $arrDetalleForm["c_pits"] = $arrCheckPits[$varIDTmpBloquedetallesCalificacionCxm];
                        
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
                                                
                          $arrayCountBloques[count($arrayCountBloques) - 1][$varBloquesCxm] = ($arrayCountBloques[count($arrayCountBloques) - 1][$varBloquesCxm] + 1);
                                              
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

                  
            }
          }
        } 
      }
    }
  }
}
   
    
  

public function actionAgregarservicio(){
  $model = new Valoracionclientenuevo();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varIdCliente = $model->id_dp_clientes;
        $varIdSociedad = $model->id_sociedad;

        $varExiste = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_valoracion_clientenuevo'])
                                    ->where(['=','id_dp_clientes',$varIdCliente])
                                    ->andwhere(['=','id_sociedad',$varIdSociedad])
                                    ->andwhere(['=','anulado',0])
                                    ->count();

        if ($varExiste == 0) {
          Yii::$app->db->createCommand()->insert('tbl_valoracion_clientenuevo',[
                    'id_dp_clientes' => $varIdCliente,
                    'id_sociedad' => $varIdSociedad,
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
          ])->execute();

          $varIdGeneral = (new \yii\db\Query())
                                    ->select(['id_clientenuevo'])
                                    ->from(['tbl_valoracion_clientenuevo'])
                                    ->where(['=','id_dp_clientes',$varIdCliente])
                                    ->andwhere(['=','id_sociedad',$varIdSociedad])
                                    ->andwhere(['=','anulado',0])
                                    ->scalar();

          return $this->redirect('index');
        }else{
          return $this->redirect(['index']);
        }
        
      }

      return $this->renderAjax('agregarservicio',[
        'model' => $model,
      ]);
    }

  }

?>


