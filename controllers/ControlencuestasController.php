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
use PHPExcel;
use PHPExcel_IOFactory;
use app\models\ControlEncuestas;
use app\models\UploadForm2;
use app\models\ControlEncuestaaci;

  class ControlEncuestasController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['prueba', 'registrarencuestas', 'guardarencuesta', 'importarexcel', 'importarexcel2', 'importarexcel3','importarexcel4','importarexcel5'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isCuadroMando();
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

    public function actionRegistrarencuestas(){
      $model = new ControlEncuestas;

      return $this->renderAjax('registrarencuestas',[
        'model' => $model,
        ]);
    }

    public function actionGuardarencuesta(){
      $txtvarIdNombreEncuesta = Yii::$app->request->get("txtvarIdNombreEncuesta");
      $txtvarIdEncuesta = Yii::$app->request->get("txtvarIdEncuesta");
      $txtvarIdComentario = Yii::$app->request->get("txtvarIdComentario");
      $txtfechacreacion = date("Y-m-d");

      Yii::$app->db->createCommand()->insert('tbl_control_encuestas',[
                                           'nombreencuesta' => $txtvarIdNombreEncuesta,
                                           'idlimeencuesta' => $txtvarIdEncuesta,
                                           'comentariosenc' => $txtvarIdComentario,
                                           'anulado' => 0,
                                           'fechacreacion' => $txtfechacreacion,
                                           'usua_id' => Yii::$app->user->identity->id,
                                       ])->execute();

      $txtRta = 0;
      die(json_encode($txtRta));
    }

    public function actionImportarexcel(){
      $model = new UploadForm2();
      $model2 = new ControlEncuestas;
      $varIDEncuesta = null;

      if (Yii::$app->request->isPost) {
        if ($model2->load(Yii::$app->request->post())) {
          $varIDEncuesta = $model2->idlimeencuesta;

          $model->file = UploadedFile::getInstance($model, 'file');

          if ($model->file && $model->validate()){
            $model->file->saveAs('categorias/' . $model->file->baseName . '.' . $model->file->extension);

            $fila = 1;
            if (($gestor = fopen('categorias/' . $model->file->baseName . '.' . $model->file->extension, "r")) !== false) {
              while (($datos = fgetcsv($gestor)) !== false) {
                $numero = count($datos);

                $fila++;
                for ($c=0; $c < $numero; $c++){
                  $varArray = $datos[$c];
                  $varDatos = explode(";", utf8_encode($varArray));

                  Yii::$app->db->createCommand()->insert('tbl_control_encuestasatu',[
                                           'idlimeencuesta' => $varIDEncuesta,
                                           'year' => $varDatos[0],
                                           'ciudad' => $varDatos[1],
                                           'director1' => $varDatos[2],
                                           'director2' => $varDatos[3],
                                           'gerente1' => $varDatos[4],
                                           'gerente2' => $varDatos[5],
                                           'gerente3' => $varDatos[6],
                                           'gerente4' => $varDatos[7],
                                           'gerente5' => $varDatos[8],
                                           'gerente6' => $varDatos[9],
                                           'gerente7' => $varDatos[10],
                                           'gerente8' => $varDatos[11],
                                           'cuenta' => $varDatos[12],
                                           'nivel' => $varDatos[13],
                                           'tipo' => $varDatos[14],
                                           'nombrecontacto' => $varDatos[15],
                                           'pregunta1' => $varDatos[16],
                                           'pregunta2' => $varDatos[17],
                                           'pregunta3' => $varDatos[18],
                                           'pregunta4' => $varDatos[19],
                                           'pregunta5' => $varDatos[20],
                                           'pregunta6' => $varDatos[21],
                                           'pregunta7' => $varDatos[22],
                                           'pregunta8' => $varDatos[23],
                                           'pregunta9' => $varDatos[24],
                                           'pregunta10' => $varDatos[25],
                                           'pregunta11' => $varDatos[26],
                                           'pregunta12' => $varDatos[27],
                                           'pregunta13' => $varDatos[28],
                                           'pregunta14' => $varDatos[29],
                                           'pregunta15' => $varDatos[30],
                                           'pregunta16' => $varDatos[31],
                                           'pregunta17' => $varDatos[32],
                                           'pregunta18' => $varDatos[33],
                                           'pregunta19' => $varDatos[34],
                                           'pregunta20' => $varDatos[35],
                                           'idtitulosp' => 0,
                                           'fechacreacion' => date("Y-m-d"),
                                           'anulado' => 0,
                                           'usua_id' => Yii::$app->user->identity->id,
                                       ])->execute();
                }
              }
              fclose($gestor);
            }
          }

          return $this->redirect('index');
        }        
      }

      return $this->renderAjax('importarexcel',[
        'model' => $model,
        'model2' => $model2,
        ]);
    }


    public function actionImportarexcel2(){
      $model = new UploadForm2();
      $model2 = new ControlEncuestas;
      $varIDEncuesta = null;

      if (Yii::$app->request->isPost) {
        if ($model2->load(Yii::$app->request->post())) {
          $varIDEncuesta = $model2->idlimeencuesta;

          $model->file = UploadedFile::getInstance($model, 'file');

          if ($model->file && $model->validate()){
            $model->file->saveAs('categorias/' . $model->file->baseName . '.' . $model->file->extension);

            $fila = 1;
            if (($gestor = fopen('categorias/' . $model->file->baseName . '.' . $model->file->extension, "r")) !== false) {
              while (($datos = fgetcsv($gestor)) !== false) {
                $numero = count($datos);

                $fila++;
                for ($c=0; $c < $numero; $c++){
                  $varArray = $datos[$c];
                  $varDatos = explode(";", utf8_encode($varArray));

                  $varDocumento = $varDatos[3];
                  $varNumeric = is_numeric($varDocumento);
                  if ($varNumeric == true) {

                    if (strlen($varDocumento) > 1) {

                      $varcc = Yii::$app->get('dbjarvis')->createCommand("select id_dp_centros_costos from dp_distribucion_personal where documento = $varDocumento and fecha_actual = (select max(fecha_actual) from dp_distribucion_personal where documento = $varDocumento)")->queryScalar();

                        $vardirector = null;
                        $vargerente = null;
                        $varcedulaJefe = null;
                        $varnombrejefe = null;
                        $varfechaingreso = null;
                        $varcliente = null;
                        $varfecharetiro = null;

                      if ($varcc != null) {
                        $vardirector = Yii::$app->get('dbjarvis')->createCommand("select distinct director_programa from dp_centros_costos where id_dp_centros_costos = $varcc")->queryScalar();

                        $vargerente = Yii::$app->get('dbjarvis')->createCommand("select distinct gerente_cuenta from dp_centros_costos where id_dp_centros_costos = $varcc")->queryScalar();

                        $varcedulaJefe = Yii::$app->get('dbjarvis')->createCommand("select documento_jefe from dp_distribucion_personal where documento = $varDocumento and fecha_actual = (select max(fecha_actual) from dp_distribucion_personal where documento = $varDocumento)")->queryScalar();

                        $varnombrejefe = Yii::$app->get('dbjarvis')->createCommand("select nombre_completo from dp_datos_generales where documento = $varcedulaJefe")->queryScalar();

                        $varfechaingreso = Yii::$app->get('dbjarvis')->createCommand("select fecha_alta  from dp_datos_generales where documento = $varDocumento")->queryScalar();

                        $varcliente = Yii::$app->get('dbjarvis')->createCommand("select distinct id_dp_clientes from dp_centros_costos where id_dp_centros_costos = $varcc")->queryScalar();

                        $varfecharetiro = Yii::$app->get('dbjarvis2')->createCommand("select fecha_baja from gh_retiros where documento = $varDocumento")->queryScalar();
                      }else{
                        $vardirector = 0;
                        $vargerente = 0;
                        $varcedulaJefe = 0;
                        $varnombrejefe = 0;
                        $varfechaingreso = null;
                        $varfecharetiro = null;
                        $varcliente = 0;
                      }

                      Yii::$app->db->createCommand()->insert('tbl_control_encuestaretiro',[
                                               'idlimeencuesta' => $varIDEncuesta,
                                               'year' => $varDatos[0],
                                               'mes' => $varDatos[1],
                                               'fecha' => $varDatos[2],
                                               'documentousuario' => $varDatos[3],
                                               'nombreusuario' => $varDatos[4],
                                               'ciudad' => $varDatos[5],
                                               'areatrabajo' => $varDatos[6],
                                               'secciontrabajo' => $varDatos[7],
                                               'roladmin' => $varDatos[8],
                                               'roloperacion' => $varDatos[9],
                                               'proceso' => $varDatos[10],
                                               'pregunta1' => $varDatos[11],
                                               'pregunta2' => $varDatos[12],
                                               'pregunta3' => $varDatos[13],
                                               'pregunta4' => $varDatos[14],
                                               'pregunta5' => $varDatos[15],
                                               'pregunta6' => $varDatos[16],
                                               'pregunta7' => $varDatos[17],
                                               'pregunta8' => $varDatos[18],
                                               'pregunta9' => $varDatos[19],
                                               'pregunta10' => $varDatos[20],
                                               'pregunta11' => $varDatos[21],
                                               'pregunta12' => $varDatos[22],
                                               'pregunta13' => $varDatos[23],
                                               'pregunta14' => $varDatos[24],
                                               'pregunta15' => $varDatos[25],
                                               'pregunta16' => $varDatos[26],
                                               'pregunta17' => $varDatos[27],
                                               'pregunta18' => $varDatos[28],
                                               'pregunta19' => $varDatos[29],
                                               'pregunta20' => $varDatos[30],
                                               'pregunta21' => $varDatos[31],
                                               'pregunta22' => $varDatos[32],
                                               'pregunta23' => $varDatos[33],
                                               'pregunta24' => $varDatos[34],
                                               'pregunta25' => $varDatos[35],
                                               'idtitulosp' => 0,
                                               'centrocostos' => $varcc,
                                               'director' => $vardirector,
                                               'gerente' => $vargerente,
                                               'jefe' => $varnombrejefe,
                                               'fechaingreso' => $varfechaingreso,
                                               'fecharetiro' => $varfecharetiro,
                                               'id_dp_clientes' => $varcliente,
                                               'fechacreacion' => date("Y-m-d"),
                                               'anulado' => 0,
                                               'usua_id' => Yii::$app->user->identity->id,
                                           ])->execute();
                    }    
                  }              
                }
              }
              fclose($gestor);
            }
          }

          return $this->redirect('index');
        }        
      }

      return $this->renderAjax('importarexcel2',[
        'model' => $model,
        'model2' => $model2,
        ]);
    }

    public function actionImportarexcel3(){
      $model = new UploadForm2();
      $model2 = new ControlEncuestas;
      $varIDEncuesta = null;

      if (Yii::$app->request->isPost) {
        if ($model2->load(Yii::$app->request->post())) {
          $varIDEncuesta = $model2->idlimeencuesta;

          $model->file = UploadedFile::getInstance($model, 'file');

          if ($model->file && $model->validate()){
            $model->file->saveAs('categorias/' . $model->file->baseName . '.' . $model->file->extension);

            $fila = 1;
            if (($gestor = fopen('categorias/' . $model->file->baseName . '.' . $model->file->extension, "r")) !== false) {
              while (($datos = fgetcsv($gestor)) !== false) {
                $numero = count($datos);

                $fila++;
                for ($c=0; $c < $numero; $c++){
                  $varArray = $datos[$c];
                  $varDatos = explode(";", utf8_encode($varArray));

                  $varDocumento = $varDatos[1];
                  $varNumeric = is_numeric($varDocumento);
                  if ($varNumeric == true) {

                    if (strlen($varDocumento) > 1) {

                      $varcc = Yii::$app->get('dbjarvis2')->createCommand("select id_dp_centros_costos from dp_distribucion_personal where documento = $varDocumento and fecha_actual = (select max(fecha_actual) from dp_distribucion_personal where documento = $varDocumento)")->queryScalar();

                      $varproceso1 = $varDatos[3];
                      $varproceso = null;
                      if ($varproceso1 == "Área Administrativa") {
                        $varproceso = $varDatos[3];
                      }else{
                        if ($varproceso1 == "Operación") {
                          $varproceso = Yii::$app->get('dbjarvis2')->createCommand("select distinct c.cliente from dp_clientes c   inner join dp_centros_costos cc on c.id_dp_clientes = cc.id_dp_clientes where cc.id_dp_centros_costos = $varcc")->queryScalar();
                        }
                      }

                      $vardirector = Yii::$app->get('dbjarvis2')->createCommand("select distinct director_programa from dp_centros_costos where id_dp_centros_costos = $varcc")->queryScalar();

                      $vargerente = Yii::$app->get('dbjarvis2')->createCommand("select distinct gerente_cuenta from dp_centros_costos where id_dp_centros_costos = $varcc")->queryScalar();

                      $varcargo = Yii::$app->get('dbjarvis2')->createCommand("select distinct p.posicion from dp_posicion p inner join dp_cargos c on p.id_dp_posicion = c.id_dp_posicion inner join dp_distribucion_personal dp on c.id_dp_cargos = dp.id_dp_cargos  where dp.documento = $varDocumento and dp.fecha_actual = (select max(fecha_actual) from dp_distribucion_personal where documento = $varDocumento)")->queryScalar();

                      $vardocjefe = Yii::$app->get('dbjarvis2')->createCommand("select distinct d.documento_jefe from dp_distribucion_personal d left join dp_distribucion_personal djefe ON djefe.documento = d.documento_jefe where   d.documento = $varDocumento and d.fecha_actual = (select max(fecha_actual) from dp_distribucion_personal where documento = $varDocumento)")->queryScalar();

                      $varnamelider = Yii::$app->get('dbjarvis2')->createCommand("select nombre_completo from dp_datos_generales where documento = $vardocjefe")->queryScalar();

                      $vardoccoordi = Yii::$app->get('dbjarvis2')->createCommand("select distinct djefe.documento_jefe from dp_distribucion_personal d left join dp_distribucion_personal djefe ON djefe.documento = d.documento_jefe where  d.documento = $varDocumento and d.fecha_actual = (select max(fecha_actual) from dp_distribucion_personal where documento = $varDocumento)")->queryScalar();

                      $varnamecoordi = Yii::$app->get('dbjarvis2')->createCommand("select nombre_completo from dp_datos_generales where documento = $vardoccoordi")->queryScalar();


                      Yii::$app->db->createCommand()->insert('tbl_control_encuestalaboral',[
                                               'idlimeencuesta' => $varIDEncuesta,
                                               'periodo' => $varDatos[0],
                                               'documentousuario' => $varDocumento,
                                               'ciudad' => $varDatos[2],
                                               'proceso' => $varproceso,
                                               'areaoperacion' => $varDatos[4],
                                               'vicepresidente' => $varDatos[5],
                                               'director' => $vardirector,
                                               'gerente' => $vargerente,
                                               'coordinador' => $varnamecoordi,
                                               'jefelider' => $varnamelider,
                                               'cargo' => $varcargo,
                                               'pregunta1' => $varDatos[6],
                                               'pregunta2' => $varDatos[7],
                                               'pregunta3' => $varDatos[8],
                                               'pregunta4' => $varDatos[9],
                                               'pregunta5' => $varDatos[10],
                                               'pregunta6' => $varDatos[11],
                                               'pregunta7' => $varDatos[12],
                                               'pregunta8' => $varDatos[13],
                                               'pregunta9' => $varDatos[14],
                                               'pregunta10' => $varDatos[15],
                                               'pregunta11' => $varDatos[16],
                                               'pregunta12' => $varDatos[17],
                                               'pregunta13' => $varDatos[18],
                                               'pregunta14' => $varDatos[19],
                                               'pregunta15' => $varDatos[20],
                                               'pregunta16' => $varDatos[21],
                                               'pregunta17' => $varDatos[22],
                                               'pregunta18' => $varDatos[23],
                                               'pregunta19' => $varDatos[24],
                                               'pregunta20' => $varDatos[25],
                                               'pregunta21' => $varDatos[26],
                                               'pregunta22' => $varDatos[27],
                                               'pregunta23' => $varDatos[28],
                                               'pregunta24' => $varDatos[29],
                                               'pregunta25' => $varDatos[30],
                                               'pregunta26' => $varDatos[31],
                                               'pregunta27' => $varDatos[32],
                                               'pregunta28' => $varDatos[33],
                                               'pregunta29' => $varDatos[34],
                                               'pregunta30' => $varDatos[35],
                                               'pregunta31' => $varDatos[36],
                                               'pregunta32' => $varDatos[37],
                                               'pregunta33' => $varDatos[38],
                                               'pregunta34' => $varDatos[39],
                                               'pregunta35' => $varDatos[40],
                                               'pregunta36' => $varDatos[41],
                                               'pregunta37' => $varDatos[42],
                                               'pregunta38' => $varDatos[43],
                                               'pregunta39' => $varDatos[44],
                                               'pregunta40' => $varDatos[45],
                                               'pregunta41' => $varDatos[46],
                                               'pregunta42' => $varDatos[47],
                                               'pregunta43' => $varDatos[48],
                                               'pregunta44' => $varDatos[49],
                                               'pregunta45' => $varDatos[50],
                                               'pregunta46' => $varDatos[51],
                                               'pregunta47' => $varDatos[52],
                                               'pregunta48' => $varDatos[53],
                                               'pregunta49' => $varDatos[54],
                                               'pregunta50' => $varDatos[55],
                                               'pregunta51' => $varDatos[56],
                                               'pregunta52' => $varDatos[57],
                                               'pregunta53' => $varDatos[58],
                                               'pregunta54' => $varDatos[59],
                                               'idtitulosp' => 0,
                                               'centrocostos' => $varcc,
                                               'fechacreacion' => date("Y-m-d"),
                                               'anulado' => 0,
                                               'usua_id' => Yii::$app->user->identity->id,
                                           ])->execute();
                    }
                  }          
                }
              }
              fclose($gestor);
            }
          }

          return $this->redirect('index');
        }        
      }

      return $this->renderAjax('importarexcel3',[
        'model' => $model,
        'model2' => $model2,        
        ]);
    }

    public function actionImportarexcel4(){
      $model = new UploadForm2();
      $model2 = new ControlEncuestas;

      if (Yii::$app->request->isPost) {
        if ($model2->load(Yii::$app->request->post())) {
          $varIDEncuesta = $model2->idlimeencuesta;

          $model->file = UploadedFile::getInstance($model, 'file');
          if ($model->file && $model->validate()){
            $model->file->saveAs('categorias/' . $model->file->baseName . '.' . $model->file->extension);

            $fila = 1;
            if (($gestor = fopen('categorias/' . $model->file->baseName . '.' . $model->file->extension, "r")) !== false) {
              while (($datos = fgetcsv($gestor)) !== false) {
                $numero = count($datos);

                $fila++;
                for ($c=0; $c < $numero; $c++){
                  $varArray = $datos[$c];
                  $varDatos = explode(";", utf8_encode($varArray));

                  $varDocumento = $varDatos[8];
                  $varNumeric = is_numeric($varDocumento);
                  if ($varNumeric == true) {
                    if (strlen($varDocumento) > 1) {
                      $varcc = Yii::$app->get('dbjarvis2')->createCommand("select id_dp_centros_costos from dp_distribucion_personal where documento = $varDocumento and fecha_actual = (select max(fecha_actual) from dp_distribucion_personal where documento = $varDocumento)")->queryScalar();

                      if (strlen($varcc) > 1) {
                        $varpcrc = substr($varcc, 0, 3);
                        
                        $varccpromotor = Yii::$app->db->createCommand("select ccpromotor from tbl_control_promotores where id_dp_cliente = $varpcrc")->queryScalar();

			if (strlen($varccpromotor) > 1) {
                          $varnombrepromotor = Yii::$app->get('dbjarvis2')->createCommand("select nombre_completo  from dp_datos_generales where documento = $varccpromotor")->queryScalar();  
                        }else{
                          $varnombrepromotor = null;  
                        }                                                                    

                        $vardirector = Yii::$app->get('dbjarvis2')->createCommand("select distinct director_programa from dp_centros_costos where id_dp_centros_costos = $varcc")->queryScalar();

                        $vargerente = Yii::$app->get('dbjarvis2')->createCommand("select distinct gerente_cuenta from dp_centros_costos where id_dp_centros_costos = $varcc")->queryScalar();

                        $vardoccoordi = Yii::$app->get('dbjarvis2')->createCommand("select distinct djefe.documento_jefe from dp_distribucion_personal d left join dp_distribucion_personal djefe ON djefe.documento = d.documento_jefe where  d.documento = $varDocumento and d.fecha_actual = (select max(fecha_actual) from dp_distribucion_personal where documento = $varDocumento)")->queryScalar();

                        $varnamecoordi = Yii::$app->get('dbjarvis2')->createCommand("select nombre_completo from dp_datos_generales where documento = $vardoccoordi")->queryScalar();
                      }else{
                        $varpcrc = null;

                        $vardirector = null;

                        $vargerente = null;

                        $vardoccoordi = null;

                        $varnamecoordi = null;

                        $varccpromotor = null;
                        $varnombrepromotor = null;
                      }                      


                      Yii::$app->db->createCommand()->insert('tbl_control_encuestaheroes',[
                                                'idlimeencuesta' => $varIDEncuesta,
                                                'iddelaencuesta' => $varDatos[0],
                                                'completado' => $varDatos[1],
                                                'ultimavista' => $varDatos[2],
                                                'lenguaje' => $varDatos[3],
                                                'passwords' => $varDatos[4],
                                                'minombre' => $varDatos[5],
                                                'micargo' => $varDatos[6],
                                                'nombreembajadorp' => $varDatos[7],
                                                'cedulaembajaor' => $varDatos[8],
                                                'ciudadembajador' => $varDatos[9],
                                                'operacionembajador' => $varDatos[10],
                                                'interaccionpopular' => $varDatos[11],
                                                'seleccionfechaip' => $varDatos[12],
                                                'asesoripingresaext' => $varDatos[13],
                                                'asesoripingresahora' => $varDatos[14],
                                                'liderformadorgrabador' => $varDatos[15],
                                                'interaccionpopularchat' => $varDatos[16],
                                                'pregunta1h' => $varDatos[17],
                                                'pregunta2h' => $varDatos[18],
                                                'pregunta3h' => $varDatos[19],
                                                'pregunta4h' => $varDatos[20],
                                                'director' => $vardirector,
                                                'gerente' => $vargerente,
                                                'coordinador' => $varnamecoordi,
                                                'centrocostos' => $varcc,
                                                'tecnicoacargo' => $varnombrepromotor,
                                                'cctecnicoacargo' => $varccpromotor,     
                                                'anulado' => 0,
                                                'fechacreacion' => date("Y-m-d"),
                                                'usua_id' =>Yii::$app->user->identity->id,
                                           ])->execute();


                    }
                  }
                }
              }
              fclose($gestor);
            }
          }
          return $this->redirect('index');
        }
      }

      return $this->renderAjax('importarexcel4',[
        'model' => $model,
        'model2' => $model2,
        ]);
    }

    public function actionImportarexcel5(){
      $model = new UploadForm2();
      $model2 = new ControlEncuestaaci;

      if (Yii::$app->request->isPost) {
        if ($model2->load(Yii::$app->request->post())) {
          $varIDEncuesta = $model2->idlimeencuesta;

          $model->file = UploadedFile::getInstance($model, 'file');
          if ($model->file && $model->validate()){
            $model->file->saveAs('categorias/' . $model->file->baseName . '.' . $model->file->extension);

            $fila = 1;
            if (($gestor = fopen('categorias/' . $model->file->baseName . '.' . $model->file->extension, "r")) !== false) {
              while (($datos = fgetcsv($gestor)) !== false) {
                $numero = count($datos);

                $fila++;
                for ($c=0; $c < $numero; $c++){
                  $varArray = $datos[$c];
                  $varDatos = explode(";", utf8_encode($varArray));

                  $varDocumento = $varDatos[0];
                  $varNumeric = is_numeric($varDocumento);
                  if ($varNumeric == true) {
                    if (strlen($varDocumento) > 1) {
                      $varcc = Yii::$app->get('dbjarvis2')->createCommand("select id_dp_centros_costos from dp_distribucion_personal where documento = $varDocumento and fecha_actual = (select max(fecha_actual) from dp_distribucion_personal where documento = $varDocumento)")->queryScalar();

                      if (strlen($varcc) > 1) {

                        $varpcrc = substr($varcc, 0, 3);       

                        $varClientes =     Yii::$app->get('dbjarvis2')->createCommand("select distinct cliente from dp_clientes where id_dp_clientes = $varpcrc")->queryScalar();             
                        
                        $varciudad = Yii::$app->get('dbjarvis2')->createCommand("select distinct ciudad from dp_centros_costos where id_dp_centros_costos = $varcc")->queryScalar();

                        $vardirector = Yii::$app->get('dbjarvis2')->createCommand("select distinct director_programa from dp_centros_costos where id_dp_centros_costos = $varcc")->queryScalar();

                        $vargerente = Yii::$app->get('dbjarvis2')->createCommand("select distinct gerente_cuenta from dp_centros_costos where id_dp_centros_costos = $varcc")->queryScalar();

                      }else{
                        $varpcrc = null;
                        $varClientes = null;
                        $varciudad = null;
                        $vardirector = null;
                        $vargerente = null;
                      }
                      

                      Yii::$app->db->createCommand()->insert('tbl_control_encuestaaci',[
                                                'idlimeencuesta' => $varIDEncuesta,
                                                'cedula' => $varDocumento,
                                                'sede' => $varDatos[1],
                                                'acipregunta1' => $varDatos[2],
                                                'acipregunta2' => $varDatos[3],
                                                'acioficina' => $varDatos[4],
                                                'acitelefonica' => $varDatos[5],
                                                'acichat' => $varDatos[6],
                                                'acicorreo' => $varDatos[7],
                                                'acimovil' => $varDatos[8],
                                                'pregunta1' => $varDatos[9],
                                                'pregunta2' => $varDatos[10],
                                                'pregunta3' => $varDatos[11],
                                                'pregunta4' => $varDatos[12],
                                                'pregunta5' => $varDatos[13],
                                                'pregunta6' => $varDatos[14],
                                                'pregunta7' => $varDatos[15],
                                                'pregunta8' => $varDatos[16],
                                                'pregunta9' => $varDatos[17],
                                                'pregunta10' => $varDatos[18],
                                                'idtitulosp' => null,
                                                'centrocostos' => $varpcrc,
                                                'director' => $vardirector,
                                                'gerente' => $vargerente, 
                                                'clientesaci' => $varClientes,
                                                'ciudadaci' => $varciudad, 
                                                'anulado' => 0,
                                                'fechacreacion' => date("Y-m-d"),
                                                'usua_id' =>Yii::$app->user->identity->id,
                                           ])->execute();

                    }
                  }
                }
              }
              fclose($gestor);
            }
          }
          return $this->redirect('index');
        }
      }

      return $this->renderAjax('importarexcel5',[
        'model' => $model,
        'model2' => $model2,
        ]);
    }


  }

?>
