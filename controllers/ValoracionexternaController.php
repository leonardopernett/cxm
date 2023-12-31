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
use app\models\Formularios;
use app\models\FormulariosSearch;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


  class ValoracionexternaController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','columnIndexFromString'],
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
        
      $varAlerta = 0;
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

          $varAlerta = 1;

          return $this->redirect(['index','varAlerta' => base64_encode($varAlerta),'id_dp_clientes' => $id_dp_clientes]);
        }

      return $this->renderAjax('agregarpcrc',[
        'model' => $model,
        'varData' => $varData,
        'id_dp_clientes' => $id_dp_clientes
      ]);
    }

   public function actionSubircarga($cod_pcrc,$id_dp_clientes){

    $model = new FormUploadtigo();
    $varAlerta = 0;


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



                        $varAlerta = 1;

                        return $this->redirect(['subircarga','id_dp_clientes' => $id_dp_clientes,'cod_pcrc' => $cod_pcrc,'varAlerta' => base64_encode($varAlerta)]);
                    }
                }
           }


      return $this->render('subircarga',[
        'model' => $model,
        'varPreguntas' => $varPreguntas,
        'varNombreArbol' => $varNombreArbol,
      ]);


   }


  private function calcularDistanciaEdicion($str1, $str2) {
    return levenshtein($str1, $str2);
  }

  private function columnIndexFromString($columnString)
  {
    $length = strlen($columnString);
    $index = 0;
    for ($i = 0; $i < $length; $i++) {
        $char = strtoupper($columnString[$i]);
        $index = $index * 26 + (ord($char) - 64);
    }
    return $index;
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
      $highestColumn = $sheet->getHighestColumn();

      $lastColumnIndex = $this->columnIndexFromString($highestColumn);

      $arraySesions = [];
      $arrayRespuestas = [];

      for ($i=2; $i <= $highestRow; $i++) {

        $varAsesor = (new \yii\db\Query())
                        ->select(['tbl_evaluados.id'])
                        ->from(['tbl_evaluados'])
                        ->where(['=','tbl_evaluados.identificacion',trim($sheet->getCell("A".$i)->getValue())])
                        ->scalar();

        $varValorador = (new \yii\db\Query())
                        ->select(['tbl_usuarios.usua_id'])
                        ->from(['tbl_usuarios'])
                        ->where(['=','tbl_usuarios.usua_identificacion',trim($sheet->getCell("B".$i)->getValue())])
                        ->scalar();

        $nombreExcel = trim($sheet->getCell("C".$i)->getValue()); // Nombre del archivo Excel
        $dimensionesBD = (new \yii\db\Query())
                      ->select(['tbl_dimensions.id', 'tbl_dimensions.name'])
                      ->from(['tbl_dimensions'])
                      ->all();

                      $minDistance = PHP_INT_MAX;
                      $varDimension = null;

                      foreach ($dimensionesBD as $dimensionBD) {
                        $distancia = $this->calcularDistanciaEdicion($nombreExcel, $dimensionBD['name']);

                        if ($distancia < $minDistance) {
                          $minDistance = $distancia;
                          $varDimension = $dimensionBD['id'];
                        }
                      }

        $varFuente = trim($sheet->getCell("D".$i)->getValue());
        $varScore = trim($sheet->getCell("E".$i)->getValue());
        $varComentarios = trim($sheet->getCell("F".$i)->getValue());

        $varForm  = (new \yii\db\Query())
                                ->select([
                                      'tbl_arbols.formulario_id'
                                ])
                                ->from(['tbl_arbols'])
                                ->where(['=','tbl_arbols.id',$cod_pcrc])
                                ->scalar();// formulario



        $varCreatedCxm = date('Y-m-d H:i:s');



        $varFechaMesActual = date('Y-m-01');

        $arrCheckPits = null;
        $arrFormulario = [];
        $arrayCountBloques = [];
        $arrayBloques = [];
        $arrCount = [];
        

        $varArbolRutaCxm = (new \yii\db\Query())
                          ->select([
                                'tbl_arbols.dsname_full'
                          ])
                          ->from(['tbl_arbols'])
                          ->where(['=','tbl_arbols.id',$cod_pcrc])
                          ->scalar();
        
        $varSesionCxm = (new \yii\db\Query())
                          ->select([
                                  'tbl_seccions.id'])
                          ->from(['tbl_seccions'])
                          ->where(['=','tbl_seccions.formulario_id',$varForm])
                          ->all();

        foreach($varSesionCxm as $valorSesion) {

          $varBloquesCxm = (new \yii\db\Query())
                          ->select(['tbl_bloques.id'])
                          ->from(['tbl_bloques'])
                          ->where(['=','tbl_bloques.seccion_id',$valorSesion['id']])
                          ->column();


          foreach ($varBloquesCxm as $bloques) {
            
            $varBloqueDetalleCxm = (new \yii\db\Query())
                          ->select(['tbl_bloquedetalles.id','tbl_bloquedetalles.calificacion_id'])
                          ->from(['tbl_bloquedetalles'])
                          ->where(['=','tbl_bloquedetalles.bloque_id',$bloques])
                          ->all();
                               
            foreach ($varBloqueDetalleCxm as $value) {
             
              for ($j=6; $j <= $lastColumnIndex; $j++) { 
              
              $rta = trim($sheet->getCellByColumnAndRow($j,$i)->getValue());
            
              $varCalificacionDetalleNameCxm = (new \yii\db\Query())
                            ->select([
                            'tbl_calificaciondetalles.name'
                            ])
                            ->from(['tbl_calificaciondetalles'])
                            ->join('INNER JOIN','tbl_bloquedetalles',
                            'tbl_bloquedetalles.calificacion_id = tbl_calificaciondetalles.calificacion_id')
                            ->where(['=','tbl_bloquedetalles.bloque_id',$bloques])
                            ->andwhere(['=','tbl_calificaciondetalles.calificacion_id',$value['calificacion_id']])
                            ->andwhere(['LIKE','tbl_calificaciondetalles.name','%' . $rta . '%',false])
                            ->scalar();
            
              $varCalificacionDetalleCxm = (new \yii\db\Query())
                            ->select([
                            'tbl_calificaciondetalles.id','tbl_calificaciondetalles.calificacion_id'
                            ])
                            ->from(['tbl_calificaciondetalles'])
                            ->where(['=','tbl_calificaciondetalles.calificacion_id',$value['calificacion_id']])                            
                            ->andwhere(['LIKE','tbl_calificaciondetalles.name','%' . trim($sheet->getCellByColumnAndRow($j,$i)->getValue()) . '%',false])
                            ->all(); 

              $varIdEquipo = (new \yii\db\Query())
                            ->select([
                                    'tbl_equipos_evaluados.equipo_id'
                            ])
                            ->from(['tbl_equipos_evaluados'])
                            ->where(['=','tbl_equipos_evaluados.evaluado_id',$varAsesor])
                            ->scalar();
    
              $varIdLider = (new \yii\db\Query())
                            ->select([
                                    'tbl_equipos.usua_id'
                            ])
                            ->from(['tbl_equipos'])
                            ->where(['=','tbl_equipos.id',$varIdEquipo])
                            ->scalar();
                            
                if (!empty($varCalificacionDetalleCxm)) {
                  if(!in_array($j, $arrCount)){                    
                    array_push($arrCount, $j);
                    array_push($arrayRespuestas,$varCalificacionDetalleCxm); //
                    break;
                  }
                  
                  
                }            

              }  
            }
          }
        }


        $varSubirCalculoCxm = (new \yii\db\Query())
                            ->select([
                                    'tbl_formularios.subi_calculo'
                            ])
                            ->from(['tbl_formularios'])
                            ->where(['=','tbl_formularios.id',$varForm])
                            ->scalar();

        $varCondicionalForm = [
                                  "usua_id" => $varValorador,
                                  "arbol_id" => $cod_pcrc,
                                  "evaluado_id" => $varAsesor,
                                  "dimension_id" => $varDimension,
                                  "basesatisfaccion_id" => null,
                                  ];
                                  
        if ($varAsesor > 0) {

          if ($varAsesor != "") {

                                      
              $varCondicionalForm = [
                                        "usua_id" => $varValorador,
                                        "arbol_id" => $cod_pcrc,
                                        "evaluado_id" => $varAsesor,
                                        "dimension_id" => $varDimension,
                                        "basesatisfaccion_id" => null,
                                        "dscomentario" => $varCreatedCxm,
                                      ];

              $idForm = \app\models\Ejecucionformularios::findOne($varCondicionalForm);
            
            if (empty($idForm)) {
                                        $varCondicional = [
                                          "usua_id" => $varValorador,
                                          "arbol_id" => $cod_pcrc,
                                          "evaluado_id" => $varAsesor,
                                          "dimension_id" => $varDimension,
                                          "basesatisfaccion_id" => null,
                                          "sneditable" => 1,
                                        ];

            
                                        
              $idTmpForm = \app\models\Tmpejecucionformularios::findOne($varCondicional);
              $arrayBloquedetallesCalificacionCxm = [];                          
              if (empty($idTmpForm)) {
                $tmpeje = new \app\models\Tmpejecucionformularios();
                $tmpeje->dimension_id = $varDimension;
                $tmpeje->arbol_id = $cod_pcrc;
                $tmpeje->usua_id = $varValorador;
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

                $arrFormulario["equipo_id"] = $varIdEquipo;
                $arrFormulario["usua_id_lider"] = $varIdLider;
                $arrFormulario["dimension_id"] = $varDimension;
                $arrFormulario["dsruta_arbol"] = $varArbolRutaCxm;
                $arrFormulario["dscomentario"] = $varComentarios;
                $arrFormulario["dsfuente_encuesta"] = $varFuente;
                $arrFormulario["transacion_id"] = 1;
                $arrFormulario["sn_mostrarcalculo"] = 1;
                
                                     
                $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                if (isset($varSubirCalculoCxm) && $varSubirCalculoCxm != '') {
                  $data->subi_calculo .= $varSubirCalculoCxm;
                  $data->save();
                }
                                      
               
                $model = \app\models\Tmpejecucionformularios::find()->where(["id" => $tmp_id])->one();
                $model->usua_id_actual = $varValorador;
                $model->save();

                
                \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);
                \app\models\Tmpejecucionsecciones::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                \app\models\Tmpejecucionbloques::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);

                foreach ($arrayRespuestas as $form_detalle_id => $calif_detalle_id) {
                  $arrDetalleForm = [];
                  $arrDetallePreg = [];
                  
                  if (count($arrCheckPits) > 0) {
                      if (isset($arrCheckPits[$form_detalle_id])) {
                          $arrDetalleForm["c_pits"] = $arrCheckPits[$form_detalle_id];
                          
                      }
                  }
                  if (empty($calif_detalle_id)) {
                      $arrDetalleForm["calificaciondetalle_id"] = -1;
          
                  }else{
                      $arrDetalleForm["calificaciondetalle_id"] = $calif_detalle_id[0]['id'];
                      $arrDetallePreg['calificacion_id'] = $calif_detalle_id[0]['calificacion_id'];
                      $arrDetallePreg['tmpejecucionformulario_id'] =  $tmp_id;
                  }
               
                  \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ["and",$arrDetallePreg]);
                  $calificacion = \app\models\Tmpejecucionbloquedetalles::findOne(["id" => $form_detalle_id]);
                 
                  if (!in_array($varBloquesCxm, $arrayBloques) && (strtoupper($varCalificacionDetalleNameCxm) == 'NA')) {
                                            
                    $arrayBloques[] = $varBloquesCxm;
                  
                    $arrayCountBloques[$count] = [($varBloquesCxm) => 1];
                    $count++;
                                          
                  } else {
                      
                    if (count($arrayCountBloques) != 0) {
                      if ((array_key_exists($varBloquesCxm, $arrayCountBloques[count($arrayCountBloques) - 1])) && (strtoupper($varCalificacionDetalleNameCxm->name) == 'NA')) {

                        $arrayCountBloques[count($arrayCountBloques) - 1][$varBloquesCxm] = ($arrayCountBloques[count($arrayCountBloques) - 1][$varBloquesCxm] + 1);
                      }
                    }
                  }
                }

                foreach ($arrayCountBloques as $dato) {
                  $totalPreguntasBloque = \app\models\Tmpejecucionbloquedetalles::find()->select("COUNT(id) as preguntas")
                                    ->from("tbl_tmpejecucionbloquedetalles")
                                    ->where(['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)])->asArray()->all();
                  if ($dato[key($dato)] == $totalPreguntasBloque["0"]["preguntas"]) {
                    \app\models\Tmpejecucionbloques::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)]);
                  }
                }

                 
                $secciones = \app\models\Tmpejecucionsecciones::findAll(['tmpejecucionformulario_id' => $tmp_id]);
                foreach ($secciones as $seccion) {
                  $bloquessnna = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                    ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                    ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                    ->where(['b.snna' => 1, 's.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                    ->groupBy("s.id")->asArray()->all();
                  $totalBloques = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                    ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                    ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                    ->where(['s.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                    ->groupBy("s.id")->asArray()->all();
                  if (count($bloquessnna) > 0) {
                    if ($bloquessnna[0]['conteo'] == $totalBloques[0]['conteo']) {
                      \app\models\Tmpejecucionsecciones::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'seccion_id' => ($seccion->seccion_id)]);
                    
                    }
                  }
                }

                
                         
                $validarPasoejecucionform = \app\models\Tmpejecucionformularios::guardarFormulario($tmp_id);


                $varIdFormulario = (new \yii\db\Query())
                        ->select(['id'])
                        ->from(['tbl_ejecucionformularios'])
                        ->where(['=','dimension_id',$varDimension])
                        ->andwhere(['=','arbol_id',$cod_pcrc])
                        ->andwhere(['=','usua_id',$varValorador])
                        ->andwhere(['=','evaluado_id',$varAsesor])
                        ->andwhere(['=','formulario_id',$varForm])
                        ->andwhere(['=','dscomentario',$varComentarios])
                        ->andwhere(['=','dsfuente_encuesta',$varFuente])
                        ->andwhere(['=','dsruta_arbol',$varArbolRutaCxm])
                        ->andwhere(['=','usua_id_lider',$varIdLider])
                        ->andwhere(['=','equipo_id',$varIdEquipo])
                        ->andwhere(['=','hora_inicial',$tmpeje['hora_inicial']])
                        ->scalar();
                      
                if ($varIdFormulario !== false) {
                  
                  Yii::$app->db->createCommand()->update('tbl_ejecucionformularios', [
                    'score' => $varScore,
                  ], 'id = :id', [':id' => $varIdFormulario])->execute();

                }

                
              }
            }
          } 
        }
      }

    }

  

  public function actionAgregarservicio(){

    $model = new Valoracionclientenuevo();
    $varAlerta = 0;

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
          $varAlerta = 1;
          return $this->redirect(['index','varAlerta' => base64_encode($varAlerta)]);
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


