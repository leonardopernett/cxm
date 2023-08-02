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


  class ApiactualizarusuariosretiradosController extends \yii\web\Controller {

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
                'actions' => ['apiactualizarusuariosretirados', 'apideshabilitarasesoresnousar'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apiactualizarusuariosretirados', 'apideshabilitarasesoresnousar'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApiactualizarusuariosretirados(){
   
      // Cantidad total de registros 
      $total_registros = (new Query())
      ->select(['COUNT(*)'])
      ->from('tbl_usuarios u')
      ->where(['u.usua_activo' => 'N'])
      ->scalar();

      $limite = 10; // Número de registros por página
      $total_paginas = ceil($total_registros/$limite); 

      for ($paginaActual = 1; $paginaActual <= $total_paginas; $paginaActual++) {

        // Calcular el offset
        $offset = ($paginaActual - 1) * $limite;

        // Traer los registros segun limit y offset
        $usuarios_deshabilitados_admin = (new Query()) 
            ->select(['u.usua_id AS id', 'u.usua_nombre AS nombre_completo', 'u.usua_usuario AS usuario_red','u.usua_identificacion AS documento', 'u.usua_activo'])
            ->from('tbl_usuarios u')
            ->where(['u.usua_activo' => 'N'])
            ->limit($limite)
            ->offset($offset)
            ->all();  

        $documentos = array_column($usuarios_deshabilitados_admin, 'documento');
        $cadena_documentos = "'" . implode("','", $documentos) . "'";

        // Ejecutar la consulta en la base de datos dbjarvis
        $activos_jarvis = Yii::$app->dbjarvis->createCommand('SELECT dp.documento, ured.usuario_red, e.tipo
        FROM dp_distribucion_personal dp
        INNER JOIN dp_estados e ON dp.id_dp_estados = e.id_dp_estados
        INNER JOIN dp_usuarios_red ured ON dp.documento = ured.documento
        WHERE e.tipo <> "RETIRO"
        AND dp.fecha_actual = DATE_FORMAT(NOW(), "%Y-%m-01")
        AND dp.documento IN (' . $cadena_documentos . ')')->queryAll();

        //Comparación de los arrays y obtener los elementos diferentes
        $usuarios_retirados_jarvis = array_udiff_assoc($usuarios_deshabilitados_admin, $activos_jarvis, function ($a, $b) {
              return ($a['usuario_red'] !== $b['usuario_red'] || $a['documento'] !== $b['documento']);
        });
      
        if(!empty($usuarios_retirados_jarvis)) {

          $usuariosCoincidentes = array_column($usuarios_retirados_jarvis, 'id');
          $cadena_usuariosCoincidentes =  "'" . implode("','", $usuariosCoincidentes) . "'";

          //Registros que no necesitan agregar el NO USAR porque ya lo tienen
          $combinacionesBuscar = array('(no usar)', '(No usar)', '(NO USAR)', 'NO USAR.');
        
          //Verificar y eliminar elementos del array que contienen el "no usar"
          foreach ($usuarios_retirados_jarvis as $clave => $resultado) {
                $nombreCompleto = $resultado['nombre_completo'];
                
                foreach ($combinacionesBuscar as $combinacion) {

                    if (stripos($nombreCompleto, $combinacion) !== false) {                  
                      unset($usuarios_retirados_jarvis[$clave]);
                      break; // Salir del bucle interno una vez que se encuentra una combinación
                    }
                }
          }

          $idsCoincidentes = array_column($usuarios_retirados_jarvis, 'id');
          $cadena_idsCoincidentes =  "'" . implode("','", $idsCoincidentes) . "'";  
          
          //usuarios que no tienen la palabra
          if( !empty($idsCoincidentes) ) {
            // Actualizar con el "no usar" 
            $comando = Yii::$app->db->createCommand('
            UPDATE tbl_usuarios
            SET usua_nombre = CONCAT("(no usar) ", usua_nombre)
            WHERE usua_id IN (' . $cadena_idsCoincidentes . ')');
            $filas_afectadas = $comando->execute();

          }

          // Actualizar la columna 'esAdmin' a '0' y agregarle 5 ceros adelante del documento
          $comando = Yii::$app->db->createCommand('
          UPDATE tbl_usuarios
          SET es_administrativo = 0,
          usua_identificacion = CONCAT("00000", usua_identificacion)
          WHERE usua_id IN (' . $cadena_usuariosCoincidentes . ')');

          $filas_afectadas = $comando->execute();
          
        }  
            
        
      } 

      die(json_encode("Usuarios Administrativos procesados correctamente"));

    }


    //Inactivar masivamente asesores que ya tienen el "NO USAR"
    public function actionApideshabilitarasesoresnousar() {

        // Cantidad total de registros 
        $total_registros = (new Query())
        ->select(['COUNT(*)'])
        ->from('tbl_evaluados ev')
        ->where(['LIKE', 'ev.name', '%no usar%', false])
        ->orWhere(['LIKE', 'ev.dsusuario_red', '%no usar%', false])
        ->scalar();

        $limite = 1000; // Número de registros por página
        $total_paginas = ceil($total_registros/$limite); 

        for ($paginaActual = 1; $paginaActual <= $total_paginas; $paginaActual++) {

            // Calcular el offset
            $offset = ($paginaActual - 1) * $limite;

            // Traer los registros segun limit y offset
            $id_usuarios_asesores = (new Query()) 
            ->select(['ev.id'])
            ->from('tbl_evaluados ev')
            ->where(['LIKE', 'ev.name', '%no usar%', false])
            ->orWhere(['LIKE', 'ev.dsusuario_red', '%no usar%', false])
            ->limit($limite)
            ->offset($offset)
            ->all();

            $idsCoincidentes = array_column($id_usuarios_asesores, 'id');
            $cadena_idsCoincidentes =  "'" . implode("','", $idsCoincidentes) . "'";

            // Actualizar la columna 'usua_activo' a 'N' para los que no aparecen con "no usar" en el nombre o el usuario de red
            $comando = Yii::$app->db->createCommand('
            UPDATE tbl_evaluados
            SET usua_activo = "N"
            WHERE id IN (' . $cadena_idsCoincidentes . ')');

            $filas_afectadas = $comando->execute();

        }

        die(json_encode("Usuarios Evaluados procesados correctamente"));

    }

  }

?>
