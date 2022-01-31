<?php

namespace app\controllers;

ini_set('upload_max_filesize', '50M');

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
use app\models\UploadForm2;
use GuzzleHttp;
use app\models\ProcesosAdministrador;
use app\models\Categoriafeedbacks;
use app\models\Tipofeedbacks;
use app\models\Dashboardpermisos;
use app\models\BaseUsuariosip;
use app\models\FormUploadtigo;


  class ProcesosadministradorController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','viewresponsability','categoriascxm','viewescucharmas','deletepermisos','viewusuariosencuestas','importarusuarios'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
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
        $model = new ProcesosAdministrador();
      
        return $this->render('index',[
            'model' => $model,
        ]);
    }

    public function actionViewresponsability(){
        $model = new ProcesosAdministrador();
        $txtConteo = 0;
        $varidarbol = null;
        $varListresponsabilidad = null;
        $varnombrepcrc = null;

        $form = Yii::$app->request->post();
        if($model->load($form)){
            $varidarbol = $model->procesos;

            $varListresponsabilidad = Yii::$app->db->createCommand("SELECT * FROM tbl_responsabilidad r WHERE r.arbol_id in ('$varidarbol')")->queryAll();
            $txtConteo = count($varListresponsabilidad);

            $varnombrepcrc = Yii::$app->db->createCommand("SELECT a.name FROM tbl_arbols a WHERE a.id in ('$varidarbol')")->queryScalar();
        }else{
          #code
        }

        return $this->render('viewresponsability',[
            'model' => $model,
            'txtConteo' => $txtConteo,
            'varidarbol' => $varidarbol,
            'varListresponsabilidad' => $varListresponsabilidad,
            'varnombrepcrc' => $varnombrepcrc,
        ]);
    }

    public function actionGetarbolesbyroles($search = null, $id = null) {
        $out = ['more' => false];
        $grupo = Yii::$app->user->identity->grupousuarioid;
        if (!is_null($search)) {
            $data = \app\models\Arboles::find()
                    ->joinWith('permisosGruposArbols')
                    ->join('INNER JOIN', 'tbl_grupos_usuarios', 'tbl_permisos_grupos_arbols.grupousuario_id = tbl_grupos_usuarios.grupos_id')
                    ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                    ->where([
                        "sncrear_formulario" => 1,
                        "snhoja" => 1,
                        "grupousuario_id" => $grupo])
                    ->andWhere(['not', ['formulario_id' => null]])
                    ->andWhere('name LIKE "%' . $search . '%" ')
                    ->andWhere('tbl_grupos_usuarios.per_realizar_valoracion = 1')
                    ->orderBy("dsorden ASC")
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $data = \app\models\Arboles::find()
                    ->joinWith('permisosGruposArbols')
                    ->join('INNER JOIN', 'tbl_grupos_usuarios', 'tbl_permisos_grupos_arbols.grupousuario_id = tbl_grupos_usuarios.grupos_id')
                    ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                    ->where([
                        "sncrear_formulario" => 1,
                        "snhoja" => 1,
                        "grupousuario_id" => $grupo])
                    ->andWhere(['not', ['formulario_id' => null]])
                    ->andWhere('tbl_arbols.id = ' . $id)
                    ->andWhere('tbl_grupos_usuarios.per_realizar_valoracion = 1')
                    ->orderBy("dsorden ASC")
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } else {
            $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
        }
        echo \yii\helpers\Json::encode($out);
    }

    public function actionGenerarregistro(){
        $varidvararboltwo = Yii::$app->request->get('txtvaridvararboltwo');
        $varidvararbol = Yii::$app->request->get('txtvaridvararbol');

        $arbolclon = Yii::$app->db->createCommand("SELECT * FROM tbl_responsabilidad r WHERE r.arbol_id in ('$varidvararboltwo')")->queryAll();

        if (count($arbolclon) != 0) {
            foreach ($arbolclon as $key => $value) {
                Yii::$app->db->createCommand()->insert('tbl_responsabilidad',[
                    'arbol_id' => $varidvararbol,
                    'nombre' => $value['nombre'],
                    'tipo' => $value['tipo'],                                          
                ])->execute();
            }
            $txtrta = 1;
        }else{
            $txtrta = 0;
        }
        

        die(json_encode($txtrta));
    }

    public function actionCategoriascxm(){
      $modelpadre = new Categoriafeedbacks();
      $modelhijo = new Tipofeedbacks();

      $dataProvider = $modelpadre->searchlist();

      $form = Yii::$app->request->post();
      if ($modelpadre->load($form)) {

        if ($modelpadre->name) {
            Yii::$app->db->createCommand()->insert('tbl_categoriafeedbacks',[
                    'name' => $modelpadre->name,                                     
                ])->execute();

          return $this->redirect('categoriascxm',['modelpadre'=>$modelpadre,'dataProvider' => $dataProvider,'modelhijo' => $modelhijo,]);
        }          
      }

      if ($modelhijo->load($form)) {
       
       if ($modelhijo->categoriafeedback_id && $modelhijo->name) {
           Yii::$app->db->createCommand()->insert('tbl_tipofeedbacks',[
                    'categoriafeedback_id' => $modelhijo->categoriafeedback_id,
                    'name' => $modelhijo->name,
                    'snaccion_correctiva' => 1,
                    'sncausa_raiz' => 1,
                    'sncompromiso' => 1,
                    'cdtipo_automatico' => 0,
                    'dsmensaje_auto' => 'Generado por el usuario',                             
                ])->execute();

            return $this->redirect('categoriascxm',['modelpadre'=>$modelpadre,'dataProvider' => $dataProvider,'modelhijo' => $modelhijo,]);
       }
            
      }

      return $this->render('categoriascxm',[
        'modelpadre' => $modelpadre,
        'dataProvider' => $dataProvider,
        'modelhijo' => $modelhijo,
      ]);
    }

    public function actionViewescucharmas(){
      $model = new Dashboardpermisos();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
          $paramsBusqueda = [':varid_dp_clientes' => $model->iddashservicio, ':anulado' => 0];

          $varNombreservicio = Yii::$app->db->createCommand('
            SELECT p.cliente FROM tbl_procesos_volumendirector p 
              WHERE p.id_dp_clientes = :varid_dp_clientes
                  AND p.anulado = :anulado
                  GROUP BY p.id_dp_clientes')->bindValues($paramsBusqueda)->queryScalar();

          Yii::$app->db->createCommand()->insert('tbl_dashboardpermisos',[
                                           'iddashservicio' => $model->iddashservicio,
                                           'usuaid' => $model->usuaid,
                                           'nombreservicio' => $varNombreservicio,
                                           'fechacreacion' => date("Y-m-d"),
                                           'anulado' => 0,
                                       ])->execute(); 

          return $this->redirect('viewescucharmas',[
              'model' => $model,
          ]);
      }

      return $this->render('viewescucharmas',[
          'model' => $model,
      ]);
    }

    public function actionDeletepermisos($id){
        Dashboardpermisos::findOne($id)->delete();

        $model = new Dashboardpermisos();

        return $this->redirect('viewescucharmas',[
            'model' => $model,
        ]);
    }

    public function actionViewusuariosencuestas(){
        $model = new BaseUsuariosip();


        return $this->render('viewusuariosencuestas',[
            'model' => $model,
        ]);
    }

    public function actionActualizaprocesos(){
        $paramsBusqueda = [':varAnulado' => 0];

        $varListSip = Yii::$app->db->createCommand('
            SELECT bu.idusuariossip, bu.usuariored, bu.usuariosip, bu.cambios FROM tbl_base_usuariosip bu
                WHERE 
                    bu.anulado = :varAnulado
                GROUP BY bu.usuariosip')->bindValues($paramsBusqueda)->queryAll();

        foreach ($varListSip as $key => $value) {
            $paramsBusquedaSip = [':varUsarioSip' => $value['usuariosip']];
            $varUsariosRed = $value['usuariored'];
            $varIdSip = $value['idusuariossip'];
            $varCambios = $value['cambios'];

            if ($varCambios == "") {
                $varListBaseSip = Yii::$app->db->createCommand('
                SELECT b.id FROM tbl_base_satisfaccion b
                    WHERE 
                     b.agente IN (:varUsarioSip)')->bindValues($paramsBusquedaSip)->queryAll();

                if (count($varListBaseSip) != 0) {
                    foreach ($varListBaseSip as $key => $value) {
                        Yii::$app->db->createCommand('
                            UPDATE tbl_base_satisfaccion 
                                SET agente = :varAgente
                                    WHERE 
                                        id = :VarId')
                            ->bindValue(':VarId', $value['id'])
                            ->bindValue(':varAgente', $varUsariosRed)
                            ->execute(); 
                    }

                    Yii::$app->db->createCommand('
                            UPDATE tbl_base_usuariosip 
                                SET fechacambios = :varFecha, cambios = :varCambios
                                    WHERE 
                                        idusuariossip = :VarIdSip')
                            ->bindValue(':VarIdSip', $varIdSip)
                            ->bindValue(':varFecha', date("Y-m-d"))
                            ->bindValue(':varCambios', 1)
                            ->execute(); 
                }
            }            
        }

        return $this->redirect('viewusuariosencuestas');
    }

    public function actionImportarusuarios(){
        $model = new FormUploadtigo();

            if ($model->load(Yii::$app->request->post()))
            {
                $model->file = UploadedFile::getInstances($model, 'file');

                if ($model->file && $model->validate()) {
                    foreach ($model->file as $file) {
                        $fecha = date('Y-m-d-h-i-s');
                        $user = Yii::$app->user->identity->username;
                        $name = $fecha . '-' . $user;
                        $file->saveAs('categorias/' . $name . '.' . $file->extension);
                        $this->Importexcelusuarios($name);

                        return $this->redirect('viewusuariosencuestas');
                    }
                }
           }

        return $this->renderAjax('importarusuarios',[
            'model' => $model,
        ]);
    }

    public function Importexcelusuarios($name){
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
            
        if ($sheet->getCell("A".$row)->getValue() != null) {

          $paramsBusqueda = [':varAsesorCC' => $sheet->getCell("A".$row)->getValue()];

          $varListaSip = Yii::$app->db->createCommand('
            SELECT COUNT(bu.idusuariossip) FROM tbl_base_usuariosip bu
              WHERE 
                bu.identificacion IN (:varAsesorCC)')->bindValues($paramsBusqueda)->queryScalar();

          if ($varListaSip == "0") {
            
            $varExisteUsuario = Yii::$app->db->createCommand('
              SELECT if(COUNT(e.id)=0,0,1) AS rta FROM tbl_evaluados e 
                WHERE 
                  e.identificacion IN (:varAsesorCC)')->bindValues($paramsBusqueda)->queryScalar();

            $varIdEvalua = 0;
            if ($varExisteUsuario != 0) {
                
              $varIdEvalua = Yii::$app->db->createCommand('
                SELECT e.id FROM tbl_evaluados e 
                  WHERE 
                    e.identificacion IN (:varAsesorCC)')->bindValues($paramsBusqueda)->queryScalar();
            }                

            Yii::$app->db->createCommand()->insert('tbl_base_usuariosip',[
                                      'usuariored' => $sheet->getCell("D".$row)->getValue(),
                                      'usuariosip' => $sheet->getCell("C".$row)->getValue(),
                                      'evaluados_id' => $varIdEvalua,
                                      'identificacion' => $sheet->getCell("A".$row)->getValue(),
                                      'comentarios' => $sheet->getCell("B".$row)->getValue(),
                                      'existeusuario' => $varExisteUsuario,
                                      'fechacreacion' => date("Y-m-d"),
                                      'anulado' => 0,
                                      'usua_id' => Yii::$app->user->identity->id,
                                      ])->execute();
          }
               
        }
      }

    }
    

  }

?>
