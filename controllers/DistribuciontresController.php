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
use GuzzleHttp;
use yii\base\Exception;
use app\models\DistribucionAsesores;
use app\models\DistribucionexternaFormularios;
use app\models\FormUploadtigo;


  class DistribuciontresController extends \yii\web\Controller {

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
                              return Yii::$app->user->identity->isAdminSistema();
                          },
                ],
              ]
            ],
          'verbs' => [          
            'class' => VerbFilter::className(),
            'actions' => [
              'delete' => ['get','post'],
            ],
          ],

          'corsFilter' => [
            'class' => \yii\filters\Cors::class,
        ],
        ];
    } 

    public function actionIndex(){
      $varListado = null;

      $varRol = (new \yii\db\Query())
                ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                        'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                        'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where(['=','tbl_usuarios.usua_id',Yii::$app->user->identity->id])
                ->scalar();

      if ($varRol == "270") {
        $varListado = (new \yii\db\Query())
                ->select([
                  'tbl_distribucionexterna_formularios.id_formularios',
                  'tbl_arbols.id',
                  'tbl_arbols.name',
                  'tbl_hojavida_sociedad.sociedad'])
                ->from('tbl_distribucionexterna_formularios')
                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                        'tbl_arbols.id = tbl_distribucionexterna_formularios.arbol_id')
                ->join('LEFT OUTER JOIN', 'tbl_hojavida_sociedad',
                        'tbl_hojavida_sociedad.id_sociedad = tbl_distribucionexterna_formularios.sociedad_id')
                ->where(['=','tbl_distribucionexterna_formularios.anulado',0])
                ->all();
      }else{
        $varListaArbols = (new \yii\db\Query())
                ->select([
                  'tbl_distribucionexterna_permisosformularios.arbol_id'])
                ->from('tbl_distribucionexterna_permisosformularios')
                ->where(['=','tbl_distribucionexterna_permisosformularios.anulado',0])
                ->andwhere(['=','tbl_distribucionexterna_permisosformularios.responsable',Yii::$app->user->identity->id])
                ->all();

        $varArrayArboles = array();
        foreach ($varListaArbols as $value) {
          array_push($varArrayArboles, $value['arbol_id']);
        }
        $listaarray = implode(", ", $varArrayArboles);
        $dataArboles = explode(",", str_replace(array("#", "'", ";", " "), '', $listaarray));

        $varListado = (new \yii\db\Query())
                ->select([
                  'tbl_distribucionexterna_formularios.id_formularios',
                  'tbl_arbols.id',
                  'tbl_arbols.name',
                  'tbl_hojavida_sociedad.sociedad'])
                ->from('tbl_distribucionexterna_formularios')
                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                        'tbl_arbols.id = tbl_distribucionexterna_formularios.arbol_id')
                ->join('LEFT OUTER JOIN', 'tbl_hojavida_sociedad',
                        'tbl_hojavida_sociedad.id_sociedad = tbl_distribucionexterna_formularios.sociedad_id')
                ->where(['=','tbl_distribucionexterna_formularios.anulado',0])
                ->andwhere(['in','tbl_distribucionexterna_formularios.arbol_id',$dataArboles])
                ->all();
      }

      $varGraficaSociedad = (new \yii\db\Query())
                ->select([
                  'COUNT(tbl_hojavida_sociedad.id_sociedad) AS varCantidad',
                  'tbl_hojavida_sociedad.sociedad'])
                ->from('tbl_hojavida_sociedad')
                ->join('LEFT OUTER JOIN', 'tbl_distribucionexterna_formularios',
                        'tbl_hojavida_sociedad.id_sociedad = tbl_distribucionexterna_formularios.sociedad_id')
                ->where(['=','tbl_distribucionexterna_formularios.anulado',0])
                ->groupby(['tbl_hojavida_sociedad.id_sociedad'])
                ->all();
      

      return $this->render('index',[
        'varListado' => $varListado,
        'varGraficaSociedad' => $varGraficaSociedad,
      ]);
    }

    public function actionParametrizarservicios(){
      $model = new DistribucionexternaFormularios();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varArbolid = $model->arbol_id;
        $varSociedadid = $model->sociedad_id;
        $varComentarios = $model->Comentarios;

        $varValida = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_distribucionexterna_formularios'])
                                ->where(['=','tbl_distribucionexterna_formularios.anulado',0])
                                ->andwhere(['=','tbl_distribucionexterna_formularios.arbol_id',$varArbolid])
                                ->count(); 

        if ($varValida == 0) {
          Yii::$app->db->createCommand()->insert('tbl_distribucionexterna_formularios',[
                      'arbol_id' => $varArbolid,
                      'sociedad_id' => $varSociedadid, 
                      'Comentarios' => $varComentarios,
                      'anulado' => 0,
                      'usua_id' =>  Yii::$app->user->identity->id,   
                      'fechacreacion' => date('Y-m-d'),                
          ])->execute();

          return $this->redirect(['index']);
        }
      }

      return $this->render('parametrizarservicios',[
        'model' => $model,
      ]);

    }

    public function actionEliminarformulario($id){
      Yii::$app->db->createCommand('
            UPDATE tbl_distribucionexterna_formularios 
                SET anulado = :varAnulado
                WHERE 
                id_formularios = :VarId')
            ->bindValue(':VarId', $id)
            ->bindValue(':varAnulado', 1)
            ->execute(); 

      return $this->redirect(['index']);
    }

    public function actionEliminarasesor($id){
      $varUsuario = (new \yii\db\Query())
                          ->select([
                            'tbl_evaluados.dsusuario_red'])
                          ->from('tbl_evaluados')
                          ->where(['=','tbl_evaluados.id',$id])
                          ->scalar();

      $varNombreAsesor = (new \yii\db\Query())
                          ->select([
                            'tbl_evaluados.name'])
                          ->from('tbl_evaluados')
                          ->where(['=','tbl_evaluados.id',$id])
                          ->scalar();

      $varDocumento = (new \yii\db\Query())
                          ->select([
                            'tbl_evaluados.identificacion'])
                          ->from('tbl_evaluados')
                          ->where(['=','tbl_evaluados.id',$id])
                          ->scalar();


      Yii::$app->db->createCommand('
            UPDATE tbl_evaluados 
                SET 
                  dsusuario_red = :varUsuarios,
                    name = :varAsesores,
                      identificacion = :varDocumentos
                WHERE 
                id = :VarId')
            ->bindValue(':VarId', $id)
            ->bindValue(':varUsuarios', $varUsuario.'(No usar)')
            ->bindValue(':varAsesores', $varNombreAsesor.'(No usar)')
            ->bindValue(':varDocumentos', '00000'.$varDocumento)
            ->execute(); 

      return $this->redirect(['index']);
    }

    public function actionIngresardistribucion($id_general){
      $model = new FormUploadtigo();

      $varIdArbol = (new \yii\db\Query())
                          ->select([
                            'tbl_distribucionexterna_formularios.arbol_id'])
                          ->from('tbl_distribucionexterna_formularios')
                          ->where(['=','tbl_distribucionexterna_formularios.anulado',0])
                          ->andwhere(['=','tbl_distribucionexterna_formularios.id_formularios',$id_general])
                          ->scalar();

      $varNombreCliente = (new \yii\db\Query())
                          ->select([
                            'tbl_arbols.name'])
                          ->from('tbl_arbols')
                          ->join('LEFT OUTER JOIN', 'tbl_distribucionexterna_formularios',
                                  'tbl_arbols.id = tbl_distribucionexterna_formularios.arbol_id')
                          ->where(['=','tbl_distribucionexterna_formularios.anulado',0])
                          ->andwhere(['=','tbl_distribucionexterna_formularios.id_formularios',$id_general])
                          ->scalar();

      $datosTablaGlobal  = (new \yii\db\Query())
                          ->select([
                            'tbl_evaluados.id', 
                            'tbl_evaluados.name AS varAsesor', 
                            'tbl_evaluados.dsusuario_red',
                            'tbl_equipos.name AS varEquipo', 
                            'tbl_equipos.usua_id'])
                          ->from('tbl_equipos')
                          ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                  'tbl_equipos.id = tbl_equipos_evaluados.equipo_id')
                          ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                  'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id')
                          ->join('LEFT OUTER JOIN', 'tbl_distribucionexterna_formularios',
                                  'tbl_evaluados.idpcrc = tbl_distribucionexterna_formularios.arbol_id')
                          ->where(['=','tbl_distribucionexterna_formularios.anulado',0])
                          ->andwhere(['=','tbl_distribucionexterna_formularios.id_formularios',$id_general])
                          ->groupby(['tbl_evaluados.id'])
                          ->all();

      if ($model->load(Yii::$app->request->post())) {

        $model->file = UploadedFile::getInstances($model, 'file');

        if ($model->file && $model->validate()) {
              
          foreach ($model->file as $file) {
            $fecha = date('Y-m-d-h-i-s');
            $user = Yii::$app->user->identity->username;
            $name = $fecha . '-' . $user;
            $file->saveAs('categorias/' . $name . '.' . $file->extension);

            if ($varIdArbol == '4041') {
              $this->ImportararchivoDidi($name,$id_general);
            }else{
              $this->ImportararchivoOtros($name,$id_general);
            }            

            $varIdFormularios = (new \yii\db\Query())
                                ->select([
                                  'tbl_distribucionexterna_formularios.id_formularios'])
                                ->from('tbl_distribucionexterna_formularios')
                                ->where(['=','tbl_distribucionexterna_formularios.anulado',0])
                                ->andwhere(['=','tbl_distribucionexterna_formularios.id_formularios',$id_general])
                                ->scalar();

            Yii::$app->db->createCommand()->insert('tbl_distribucionexterna_ultimaactualizacion',[
                      'id_formularios' => $varIdFormularios,
                      'fecha_hora' => date('Y-m-d H:i:s'),
                      'anulado' => 0,
                      'fechacreacion' =>  date('Y-m-d'), 
                      'usua_id' => Yii::$app->user->identity->id,                                 
            ])->execute();


            return $this->redirect(array('ingresardistribucion','id'=>$id_general));
          }
        }
      }


      return $this->render('ingresardistribucion',[
        'varNombreCliente' => $varNombreCliente,
        'datosTablaGlobal' => $datosTablaGlobal,
        'model' => $model,
      ]);
    }

    public function ImportararchivoOtros($name,$id_general){
      $varSocidadId = (new \yii\db\Query())
                                    ->select(['tbl_distribucionexterna_formularios.sociedad_id'])
                                    ->from(['tbl_distribucionexterna_formularios'])
                                    ->where(['=','tbl_distribucionexterna_formularios.arbol_id',$id_general])
                                    ->scalar(); 

      $varNombreArbol = (new \yii\db\Query())
                                    ->select(['tbl_arbols.name'])
                                    ->from(['tbl_arbols'])
                                    ->where(['=','tbl_arbols.id',$id_general])
                                    ->scalar(); 

      $varDocumentoAsesor = null;
      $varDocumentosLider = null;

      $varExisteLider = null;
      $varValidaAsesorCXM = null;

      $varValidaAccion = 1;

      $inputFileOtros = 'categorias/'. $name .  '.xlsx';

      try {
          $inputFileType = \PHPExcel_IOFactory::identify($inputFileOtros);
          $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
          $objPHPExcel = $objReader->load($inputFileOtros);

      } catch (Exception $e) {
          die('Error');
      }

      $sheet = $objPHPExcel->getSheet(0);
      $highestRow = $sheet->getHighestRow();
      for ($i=12; $i <= $highestRow; $i++) { 

        // Se empieza con el Asesor para generar la validacion.
        $varUsuarioRed = $sheet->getCell("A".$i)->getValue();
        $varDocumentoAsesor = null;
        $varNombresAsesor = null;
        $varUsuaRed = null;
        $varDocumentoLider = null;
        $varNombresLider = null;
        $varUsuaRedLider = null;

        $paramsAsesorRed = [':varUsuarioAsesor'=>$varUsuarioRed];
        if (is_numeric($varUsuarioRed)) {
          $varDatosAsesor = Yii::$app->dbjarvis->createCommand('
              SELECT 
                documento, usuario_red, nombre 
              FROM dp_usuarios_red 
                WHERE 
                  dp_usuarios_red.documento = :varUsuarioAsesor')->bindValues($paramsAsesorRed)->queryAll();
        }else{
          $varDatosAsesor = Yii::$app->dbjarvis->createCommand('
              SELECT 
                documento, usuario_red, nombre 
              FROM dp_usuarios_red 
                WHERE 
                  dp_usuarios_red.usuario_red = :varUsuarioAsesor')->bindValues($paramsAsesorRed)->queryAll();
        }

        if (count($varDatosAsesor) != 0) {

          foreach ($varDatosAsesor as $value) {
            $varDocumentoAsesor = $value['documento'];
            $varNombresAsesor = $value['nombre'];
            $varUsuaRed = $value['usuario_red'];
          }

          $varValidaAsesorCXM = (new \yii\db\Query())
                                ->select([
                                  'tbl_evaluados.id'])
                                ->from('tbl_evaluados')
                                ->where(['=','tbl_evaluados.identificacion',$varDocumentoAsesor])
                                ->scalar();
          
          if (count($varValidaAsesorCXM) == 0) {
            Yii::$app->db->createCommand()->insert('tbl_evaluados',[
                      'name' => $varNombresAsesor,
                      'telefono' => null,
                      'dsusuario_red' => $varUsuaRed,
                      'cdestatus' => null,
                      'identificacion' => $varDocumentoAsesor,
                      'email' => $varUsuaRed.'@grupokonecta.co',  
                      'idpcrc' => $id_general,
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),                                  
            ])->execute();
          }

        }else{

          // Aqui generar una validacion mas dp_datos_generales y me traigo la cedula y el nombre, el usuario de red sera la misma cedula.
          $varDatosAsesor = Yii::$app->dbjarvis->createCommand('
            SELECT 
              dp_datos_generales.documento, 
              CONCAT(dp_datos_generales.primer_apellido," ",dp_datos_generales.segundo_apellido," ",dp_datos_generales.primer_nombre," ",dp_datos_generales.segundo_nombre) AS nombre 
            FROM dp_datos_generales 
              WHERE 
                dp_datos_generales.documento = :varUsuarioAsesor')->bindValues($paramsAsesorRed)->queryAll();

          if (count($varDatosAsesor) != 0) {
            // Valido si existe en datos generales sino no lo crea y se agrega los datos a la tabla tbl_distribucionexterna_sinjarvis
            foreach ($varDatosAsesor as $value) {
              $varDocumentoAsesor = $value['documento'];
              $varNombresAsesor = $value['nombre'];
              $varUsuaRed = $value['documento'];
            }

            $varValidaAsesorCXM = (new \yii\db\Query())
                                  ->select([
                                    'tbl_evaluados.id'])
                                  ->from('tbl_evaluados')
                                  ->where(['=','tbl_evaluados.identificacion',$varDocumentoAsesor])
                                  ->scalar();
            
            if (count($varValidaAsesorCXM) == 0) {
              Yii::$app->db->createCommand()->insert('tbl_evaluados',[
                        'name' => $varNombresAsesor,
                        'telefono' => null,
                        'dsusuario_red' => $varUsuaRed,
                        'cdestatus' => null,
                        'identificacion' => $varDocumentoAsesor,
                        'email' => $varUsuaRed.'@grupokonecta.co',  
                        'idpcrc' => $id_general,
                        'usua_id' => Yii::$app->user->identity->id,
                        'fechacreacion' => date('Y-m-d'),                                  
              ])->execute();
            }

          }else{
            Yii::$app->db->createCommand()->insert('tbl_distribucionexterna_sinjarvis',[
                      'documento_usuario' => $varUsuarioRed,
                      'tipo_usuario' => 1,
                      'comentarios' => null,
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                                  
            ])->execute();

            $varValidaAccion = 2;
          }
          
        }

        
        // Se empieza a validar Lider.
        $varLiderRed = $sheet->getCell("C".$i)->getValue();       

        $paramsLiderRed = [':varLideresRed' => $varLiderRed];
        if (is_numeric($varLiderRed)) {
          
          $varDocumentosLider = Yii::$app->dbjarvis->createCommand('
              SELECT 
                documento, usuario_red, nombre 
              FROM dp_usurios_red 
                WHERE 
                  dp_usuarios_red.documento = :varLideresRed')->bindValues($paramsLiderRed)->queryAll();
        }else{
          
          $varDocumentosLider = Yii::$app->dbjarvis->createCommand('
              SELECT 
                documento, usuario_red, nombre 
              FROM dp_usuarios_red 
                WHERE 
                  dp_usuarios_red.usuario_red = :varLideresRed')->bindValues($paramsLiderRed)->queryAll();
        }
        
        if (count($varDocumentosLider) != 0) {
          foreach ($varDocumentosLider as $value) {
            $varDocumentoLider = $value['documento'];
            $varNombresLider = $value['nombre'];
            $varUsuaRedLider = $value['usuario_red'];
          }
          
          $varExisteLider = (new \yii\db\Query())
                                    ->select(['usua_id'])
                                    ->from(['tbl_usuarios'])
                                    ->where(['=','usua_identificacion',$varDocumentoLider])
                                    ->scalar(); 
                                                                   
          if ($varExisteLider == 0) {
            $paramsBuscaLider = [':DocumentoLider'=>$varDocumentoLider];
            
            $varNombreLider = Yii::$app->dbjarvis->createCommand('
              SELECT CONCAT(dg.primer_apellido," ",dg.segundo_apellido," ",dg.primer_nombre," ",dg.segundo_nombre) AS NombreCompleto, du.usuario_red FROM dp_datos_generales dg
                INNER JOIN dp_usuarios_red du ON
                  dg.documento = du.documento 
              WHERE 
                du.documento = :DocumentoLider ')->bindValues($paramsBuscaLider)->queryScalar();

            $varUsuaredLider = Yii::$app->dbjarvis->createCommand('
              SELECT du.usuario_red FROM  dp_usuarios_red du 
              WHERE 
                du.documento = :DocumentoLider ')->bindValues($paramsBuscaLider)->queryScalar();

            if ($varNombreLider != "" && $varUsuaredLider != "") {
              Yii::$app->db->createCommand()->insert('tbl_usuarios',[
                      'usua_usuario' => $varUsuaredLider,
                      'usua_nombre' => $varNombreLider,
                      'usua_email' => $varUsuaredLider.'@grupokonecta.com',
                      'usua_identificacion' => $varDocumentoLider,
                      'usua_activo' => "S",
                      'usua_estado' => "D",  
                      'usua_fechhoratimeout' => null,
                      'fechacreacion' =>  date('Y-m-d'), 
                      'id_sociedad' => $varSocidadId,                                 
                  ])->execute();

              $varidUsuarioLider = (new \yii\db\Query())
                                    ->select(['usua_id'])
                                    ->from(['tbl_usuarios'])
                                    ->where(['=','usua_identificacion',$varDocumentoLider])
                                    ->scalar(); 

              Yii::$app->db->createCommand()->insert('rel_usuarios_roles',[
                      'rel_usua_id' => $varidUsuarioLider,
                      'rel_role_id' => 273,                               
                  ])->execute();

              Yii::$app->db->createCommand()->insert('rel_grupos_usuarios',[
                      'usuario_id' => $varidUsuarioLider,
                      'grupo_id' => 1,                               
                  ])->execute();

              $varValidaEquipoLider = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_equipos'])
                                    ->where(['=','tbl_equipos.usua_id',$varidUsuarioLider])
                                    ->count(); 

              if ($varValidaEquipoLider == 0) {
                Yii::$app->db->createCommand()->insert('tbl_equipos',[
                      'name' => $varNombreLider.'_'.$varNombreArbol,
                      'nmumbral_verde' => 1,
                      'nmumbral_amarillo' => 1,
                      'usua_id' => $varidUsuarioLider,     
                ])->execute();

              }

            }

          }
        }else{
          Yii::$app->db->createCommand()->insert('tbl_distribucionexterna_sinjarvis',[
                      'documento_usuario' => $varLiderRed,
                      'tipo_usuario' => 2,
                      'comentarios' => null,
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                                  
          ])->execute();

          $varValidaAccion = 2;
        }

        
        
        // Se empieza a Gestionar el Equipo de los Lideres con los Asesores
        if ($varValidaAccion == 1) {


          
          $varValidaAsesorConEquipo = (new \yii\db\Query())
                                    ->select(['tbl_equipos_evaluados.equipo_id'])
                                    ->from(['tbl_equipos_evaluados'])
                                    ->where(['=','tbl_equipos_evaluados.evaluado_id',$varValidaAsesorCXM])
                                    ->scalar(); 

          $varValidarEquipoLider = (new \yii\db\Query())
                                    ->select(['tbl_equipos.id'])
                                    ->from(['tbl_equipos'])
                                    ->where(['=','tbl_equipos.usua_id',$varExisteLider])
                                    ->scalar(); 

          if ($varValidaAsesorConEquipo) {            

            if ($varValidarEquipoLider != $varValidaAsesorConEquipo) {
              $varIdEquipoEvaluado = (new \yii\db\Query())
                                    ->select(['tbl_equipos_evaluados.id'])
                                    ->from(['tbl_equipos_evaluados'])
                                    ->where(['=','tbl_equipos_evaluados.evaluado_id',$varValidaAsesorCXM])
                                    ->scalar(); 

              $paramsEliminar = [':IdControl'=>$varIdEquipoEvaluado];          

              Yii::$app->db->createCommand('
                    DELETE FROM tbl_equipos_evaluados 
                      WHERE 
                        id = :IdControl')
              ->bindValues($paramsEliminar)
              ->execute();

              Yii::$app->db->createCommand()->insert('tbl_equipos_evaluados',[
                      'evaluado_id' => $varValidaAsesorCXM,
                      'equipo_id' => $varValidarEquipoLider,                   
              ])->execute();
            }

          }else{
            Yii::$app->db->createCommand()->insert('tbl_equipos_evaluados',[
                      'evaluado_id' => $varValidaAsesorCXM,
                      'equipo_id' => $varValidarEquipoLider,                   
              ])->execute();
          }

        }


      }

    }

    public function ImportararchivoDidi($name,$id_general){
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


      for ($i=12; $i <= $highestRow; $i++) {  
        
       
        $username = $sheet->getCell("A".$i)->getValue();
               
          do{
            $identificacion = rand(10000000,99999999);
            $identificadores = (new \yii\db\Query())
                ->select(['*'])
                ->from(['tbl_evaluados'])   
                ->where(['=', 'identificacion', $identificacion])                               
                ->count();

          }while($identificadores > 0);            
        
          $nombre_equipo = $sheet->getCell("C".$i)->getValue();                     

          Yii::$app->db->createCommand()->insert('tbl_evaluados',[
            'dsusuario_red' => $username,
            'name' => $sheet->getCell("B".$i)->getValue(),
            'identificacion' => $identificacion,
            'email' =>  $username.'@cxm.com.co',
            'fechacreacion' => date("Y-m-d"),
            'usua_id' => Yii::$app->user->identity->id, 
            'idpcrc' => 4041
          ])->execute();
            

          $id_equipo = (new \yii\db\Query())
            ->select(['id'])
            ->from(['tbl_equipos'])
            ->where(['=','name',$nombre_equipo])
            ->all();

          
          $id_evaluado = (new \yii\db\Query())
            ->select(['id'])
            ->from(['tbl_evaluados'])    
            ->where(['=', 'dsusuario_red', $username])
            ->all();          
          
          

          Yii::$app->db->createCommand()->insert('tbl_equipos_evaluados',[
              'evaluado_id' => $id_evaluado[0]["id"],
              'equipo_id' => $id_equipo[0]["id"],
          ])->execute();

      }   
    }



  }

?>
