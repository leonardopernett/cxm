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
use GuzzleHttp;
use Exception;

  class ValoracionexternaController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','agregarvaloraciones'],
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
                                  'tbl_proceso_cliente_centrocosto.cliente', 
                                  'tbl_hojavida_sociedad.sociedad'
                                ])
                                ->from(['tbl_valoracion_clientenuevo'])
                                ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                                  'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_valoracion_clientenuevo.id_dp_clientes')
                                ->join('LEFT OUTER JOIN', 'tbl_hojavida_sociedad',
                                  'tbl_hojavida_sociedad.id_sociedad = tbl_valoracion_clientenuevo.id_sociedad')
                                ->where(['=','tbl_valoracion_clientenuevo.anulado',0])
                                ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                ->all();  
      
      return $this->render('index',[
        'varListaGeneral' => $varListaGeneral,
      ]);
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

          return $this->redirect(array('agregarparametros','id_general'=>$varIdGeneral));
        }else{
          return $this->redirect(['index']);
        }
        
      }

      return $this->renderAjax('agregarservicio',[
        'model' => $model,
      ]);
    }

    public function actionAgregarparametros($id_general){
      $model = new Valoraciondatogeneral();

      $varListaInteracciones = (new \yii\db\Query())
                                    ->select([
                                      'tbl_valoracion_atributos.id_atributo',
                                      'tbl_valoracion_atributos.atributos', 
                                      'tbl_valoracion_atributos.resp_atributos'])
                                    ->from(['tbl_valoracion_atributos'])
                                    ->where(['=','tbl_valoracion_atributos.id_clientenuevo',$id_general])
                                    ->andwhere(['=','tbl_valoracion_atributos.anulado',0])
                                    ->all();

      $varListaItemsEspeciales = (new \yii\db\Query())
                                    ->select([
                                      'tbl_valoracion_datoespecial.id_datoespecial',
                                      'tbl_valoracion_datoespecial.item_especial', 
                                      'tbl_valoracion_datoespecial.campo_especial'])
                                    ->from(['tbl_valoracion_datoespecial'])
                                    ->where(['=','tbl_valoracion_datoespecial.id_clientenuevo',$id_general])
                                    ->andwhere(['=','tbl_valoracion_datoespecial.anulado',0])
                                    ->all();

      $varListaFormularios = (new \yii\db\Query())
                                ->select([
                                  'tbl_valoracion_formulariosexcel.id_formulariosexcel',
                                  'tbl_valoracion_formulariosexcel.servicio_excel', 
                                  'tbl_arbols.name'
                                ])
                                ->from(['tbl_valoracion_formulariosexcel'])
                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_valoracion_formulariosexcel.formulario_cxm')
                                ->where(['=','tbl_valoracion_formulariosexcel.anulado',0])
                                ->andwhere(['=','tbl_valoracion_formulariosexcel.id_clientenuevo',$id_general])
                                ->all(); 

      $varCliente = (new \yii\db\Query())
                                ->select([
                                  'tbl_proceso_cliente_centrocosto.cliente'
                                ])
                                ->from(['tbl_proceso_cliente_centrocosto'])
                                ->join('LEFT OUTER JOIN', 'tbl_valoracion_clientenuevo',
                                  'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_valoracion_clientenuevo.id_dp_clientes')
                                ->where(['=','tbl_valoracion_clientenuevo.anulado',0])
                                ->andwhere(['=','tbl_valoracion_clientenuevo.id_clientenuevo',$id_general])
                                ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                ->scalar();  

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varidcc_asesor = $model->cc_asesor;
        $variddimension = $model->dimension;
        $varidcc_valorador = $model->cc_valorador;
        $varidscore = $model->score;
        $varidarbol_id = $model->arbol_id;
        $varidcomentario = $model->comentario;

        Yii::$app->db->createCommand()->insert('tbl_valoracion_datogeneral',[
                    'id_clientenuevo' => $id_general,
                    'cc_asesor' => $varidcc_asesor,
                    'dimension' => $variddimension,
                    'cc_valorador' => $varidcc_valorador,
                    'score' => $varidscore,
                    'arbol_id' => $varidarbol_id,
                    'comentario' => $varidcomentario,
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
        ])->execute();

        return $this->redirect(['index']);
      }

      return $this->render('agregarparametros',[
        'id_general' => $id_general,
        'varCliente' => $varCliente,
        'model' => $model,
        'varListaInteracciones' => $varListaInteracciones,
        'varListaItemsEspeciales' => $varListaItemsEspeciales,
        'varListaFormularios' => $varListaFormularios,
      ]);
    }

    public function actionAgregarinteracciones($id_general){
      $model = new Valoracionatributos();      

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varPregunta = $model->atributos;
        $varExcelRespuesta = $model->resp_atributos;

        Yii::$app->db->createCommand()->insert('tbl_valoracion_atributos',[
                    'id_clientenuevo' => $id_general,
                    'atributos' => $varPregunta,
                    'resp_atributos' => $varExcelRespuesta,
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
        ])->execute();

        return $this->redirect(array('agregarparametros','id_general'=>$id_general));

      }

      return $this->renderAjax('agregarinteracciones',[
        'model' => $model,
        'id_general' => $id_general,
      ]);
    }

    public function actionEliminarinteracciones($id_Interaccion,$id_general){

      Yii::$app->db->createCommand()->update('tbl_valoracion_atributos',[
                      'anulado' => 1,                       
        ],'id_clientenuevo ='.$id_general.' AND id_atributo = '.$id_Interaccion.'')->execute();

      return $this->redirect(array('agregarparametros','id_general'=>$id_general));

    }

    public function actionAgregarespeciales($id_general){

      $model = new Valoraciondatoespecial();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varItem = $model->item_especial;
        $varExcelItem = $model->campo_especial;

        Yii::$app->db->createCommand()->insert('tbl_valoracion_datoespecial',[
                    'id_clientenuevo' => $id_general,
                    'item_especial' => $varItem,
                    'campo_especial' => $varExcelItem,
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
        ])->execute();

        return $this->redirect(array('agregarparametros','id_general'=>$id_general));

      }

      return $this->renderAjax('agregarespeciales',[
        'id_general' => $id_general,
        'model' => $model,
      ]);
    }

    public function actionEliminaritems($id_Items,$id_general){

      Yii::$app->db->createCommand()->update('tbl_valoracion_datoespecial',[
                      'anulado' => 1,                       
        ],'id_clientenuevo ='.$id_general.' AND id_datoespecial = '.$id_Items.'')->execute();

      return $this->redirect(array('agregarparametros','id_general'=>$id_general));

    }

    public function actionVerexterna($id_general){

      $varDatoGeneral = (new \yii\db\Query())
                                    ->select([
                                      'tbl_valoracion_datogeneral.id_datogeneral'
                                    ])
                                    ->from(['tbl_valoracion_datogeneral'])
                                    ->where(['=','tbl_valoracion_datogeneral.id_clientenuevo',$id_general])
                                    ->andwhere(['=','tbl_valoracion_datogeneral.anulado',0])
                                    ->scalar();

      $model = Valoraciondatogeneral::findOne($varDatoGeneral);

      $varListaInteracciones_string = (new \yii\db\Query())
                                    ->select([
                                      'tbl_valoracion_atributos.id_atributo',
                                      'tbl_valoracion_atributos.atributos', 
                                      'tbl_valoracion_atributos.resp_atributos'])
                                    ->from(['tbl_valoracion_atributos'])
                                    ->where(['=','tbl_valoracion_atributos.id_clientenuevo',$id_general])
                                    ->andwhere(['=','tbl_valoracion_atributos.anulado',0])
                                    ->all();

      $varListaItemsEspeciales_string = (new \yii\db\Query())
                                    ->select([
                                      'tbl_valoracion_datoespecial.id_datoespecial',
                                      'tbl_valoracion_datoespecial.item_especial', 
                                      'tbl_valoracion_datoespecial.campo_especial'])
                                    ->from(['tbl_valoracion_datoespecial'])
                                    ->where(['=','tbl_valoracion_datoespecial.id_clientenuevo',$id_general])
                                    ->andwhere(['=','tbl_valoracion_datoespecial.anulado',0])
                                    ->all();

      $varListaFormularios_string = (new \yii\db\Query())
                                ->select([
                                  'tbl_valoracion_formulariosexcel.id_formulariosexcel',
                                  'tbl_valoracion_formulariosexcel.servicio_excel', 
                                  'tbl_arbols.name'
                                ])
                                ->from(['tbl_valoracion_formulariosexcel'])
                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                  'tbl_arbols.id = tbl_valoracion_formulariosexcel.formulario_cxm')
                                ->where(['=','tbl_valoracion_formulariosexcel.anulado',0])
                                ->andwhere(['=','tbl_valoracion_formulariosexcel.id_clientenuevo',$id_general])
                                ->all(); 

      $varCliente_string = (new \yii\db\Query())
                                ->select([
                                  'tbl_proceso_cliente_centrocosto.cliente'
                                ])
                                ->from(['tbl_proceso_cliente_centrocosto'])
                                ->join('LEFT OUTER JOIN', 'tbl_valoracion_clientenuevo',
                                  'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_valoracion_clientenuevo.id_dp_clientes')
                                ->where(['=','tbl_valoracion_clientenuevo.anulado',0])
                                ->andwhere(['=','tbl_valoracion_clientenuevo.id_clientenuevo',$id_general])
                                ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                ->scalar();

      return $this->render('verexterna',[
        'model' => $model,
        'varListaInteracciones_string' => $varListaInteracciones_string,
        'varListaItemsEspeciales_string' => $varListaItemsEspeciales_string,
        'varCliente_string' => $varCliente_string,
        'varListaFormularios_string' => $varListaFormularios_string,
      ]);
    }

    public function actionAgregarformularios($id_general){
      $model = new Valoracionformulariosexcel();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varClienteExcel = $model->servicio_excel;
        $varClienteCxm = $model->formulario_cxm;

        Yii::$app->db->createCommand()->insert('tbl_valoracion_formulariosexcel',[
                    'id_clientenuevo' => $id_general,
                    'servicio_excel' => $varClienteExcel,
                    'formulario_cxm' => $varClienteCxm,
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
        ])->execute();

        return $this->redirect(array('agregarparametros','id_general'=>$id_general));
      }

      return $this->render('agregarformularios',[
        'model' => $model,
        'id_general' => $id_general,
      ]);
    }

    public function actionEliminarforms($id_Forms,$id_general){

      Yii::$app->db->createCommand()->update('tbl_valoracion_formulariosexcel',[
                      'anulado' => 1,                       
        ],'id_clientenuevo ='.$id_general.' AND id_formulariosexcel = '.$id_Forms.'')->execute();

      return $this->redirect(array('agregarparametros','id_general'=>$id_general));

    }

    public function actionAgregarvaloraciones($id_general){
      $model = new FormUploadtigo();

            if ($model->load(Yii::$app->request->post()))
            {
                $model->file = UploadedFile::getInstances($model, 'file');

                if ($model->file && $model->validate()) {
                    foreach ($model->file as $file) {
                        $fecha = date('Y-m-d-h-i-s');
                        $user = Yii::$app->user->identity->username;
                        $name = $fecha . '-' . $user . '-Meli';
                        $file->saveAs('categorias/' . $name . '.' . $file->extension);
                        $this->Importexcelvaloracion($name,$id_general);

                        die(json_encode("Aqui vamos"));

                        // return $this->redirect('viewusuariosencuestas');
                    }
                }
           }
      

      return $this->render('agregarvaloraciones',[
        'model' => $model,
      ]);
    }

    public function Importexcelvaloracion($name,$id_general){
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

        for ($row = 2; $row <= $highestRow; $row++) { 
            var_dump($sheet->getCell("A".$row)->getValue());
            var_dump($sheet->getCell("B".$row)->getValue());
            var_dump($sheet->getCell("C".$row)->getValue());
            var_dump($sheet->getCell("D".$row)->getValue());
            var_dump($sheet->getCell("E".$row)->getValue());
            var_dump($sheet->getCell("F".$row)->getValue());
            var_dump($sheet->getCell("G".$row)->getValue());
            var_dump($sheet->getCell("H".$row)->getValue());
            var_dump($sheet->getCell("I".$row)->getValue());
            var_dump($sheet->getCell("J".$row)->getValue());
            var_dump($sheet->getCell("K".$row)->getValue());
            var_dump($sheet->getCell("L".$row)->getValue());
            var_dump($sheet->getCell("M".$row)->getValue());
            var_dump($sheet->getCell("N".$row)->getValue());
        }

      var_dump("Aqui estamos");
    }


  }

?>


