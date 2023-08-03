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

class ApiactualizaradministrativoscxmController extends \yii\web\Controller {

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
                'actions' => ['apibuscarusuariosactivosjarvis'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apibuscarusuariosactivosjarvis'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApibuscarusuariosactivosjarvis() {

        // Cantidad total de registros 
        $total_registros = (new Query())
        ->select(['COUNT(*)'])
        ->from('tbl_usuarios u')
        ->where(['u.usua_activo' => 'S'])
        ->scalar();

        $limite = 1000; // Número de registros por página
        $total_paginas = ceil($total_registros/$limite);

        for ($paginaActual = 1; $paginaActual <= $total_paginas; $paginaActual++) {

            // Calcular el offset
            $offset = ($paginaActual - 1) * $limite;

            // Traer los registros segun limit y offset
            $usuarios_administrativos = (new Query()) 
            ->select(['u.usua_id AS id', 'u.usua_nombre AS nombre_completo',
            'u.usua_usuario AS usuario_red','u.usua_identificacion AS documento',
            'u.usua_activo', 'u.es_administrativo'])
            ->from('tbl_usuarios u')
            ->where(['u.usua_activo' => 'S'])
            ->limit($limite)
            ->offset($offset)
            ->all();

            $array_deshabilitar_usuarios= [];
            $array_habilitar_usuarios= [];

            foreach ($usuarios_administrativos as $info_usuario) {
                $id_usuario = $info_usuario['id'];
                $usuario_red_cxm = $info_usuario['usuario_red'];
                $cc_usuario = $info_usuario['documento'];
                $es_administrativo = $info_usuario['es_administrativo'];
                $nombre_completo_usua_jarvis = "";
                $documento_usua_jarvis = "";

                $usuario_no_admin = ["id" => $id_usuario, "nombre_completo" => $info_usuario['nombre_completo'], "usuario_red" => $info_usuario['usuario_red']];
                $usuario_admin = ["id" => $id_usuario, "es_administrativo" => $es_administrativo, "nombre_completo_jarvis" => $nombre_completo_usua_jarvis, "documento_jarvis" => $documento_usua_jarvis];

                $paramsBuscaDocumento = [':documento'=>$cc_usuario];
                $activos_jarvis = Yii::$app->dbjarvis->createCommand('SELECT
                CONCAT(datos.primer_apellido, " ", datos.segundo_apellido, " ", datos.primer_nombre, " ", datos.segundo_nombre) AS nombre_completo,
                LOWER(ured.usuario_red) usuario_red, dp.fecha_actual, dp.documento,
                e.tipo, ct.cargo_tipo AS tipocargo_principal, cp.posicion, dp.cargos_encargo, encargo_t.cargo_tipo AS tipocargo_encargo 
                FROM dp_distribucion_personal dp
                LEFT JOIN dp_estados e ON dp.id_dp_estados = e.id_dp_estados 
                LEFT JOIN dp_usuarios_red ured ON dp.documento = ured.documento 
                LEFT JOIN dp_cargos c ON dp.id_dp_cargos = c.id_dp_cargos 
                LEFT JOIN dp_posicion cp ON c.id_dp_posicion = cp.id_dp_posicion
                LEFT JOIN dp_cargos_tipo ct ON c.id_dp_cargos_tipo = ct.id_dp_cargos_tipo
                LEFT JOIN dp_cargos_rol cr ON c.id_dp_cargos_rol = cr.id_dp_cargos_rol
                LEFT JOIN dp_cargos encargo ON dp.cargos_encargo= encargo.id_dp_cargos
                LEFT JOIN dp_posicion encargo_p ON encargo.id_dp_posicion = encargo_p.id_dp_posicion
                LEFT JOIN dp_cargos_tipo encargo_t ON encargo.id_dp_cargos_tipo = encargo_t.id_dp_cargos_tipo
                LEFT JOIN dp_cargos_rol encargo_r ON encargo.id_dp_cargos_rol = encargo_r.id_dp_cargos_rol
                LEFT JOIN dp_datos_generales datos  ON dp.documento = datos.documento
                WHERE e.tipo <> "RETIRO"  
                AND dp.fecha_actual = DATE_FORMAT(NOW() ,"%Y-%m-01")
                AND dp.documento = :documento')->bindValues($paramsBuscaDocumento)->queryAll();

                $cantidad_registros_encontrados = count($activos_jarvis);  
                
                if($cantidad_registros_encontrados==0){

                    $array_deshabilitar_usuarios[] = $usuario_no_admin; //esAdmin=0 y poner no usar                   
                    continue;         
                }

                if($cantidad_registros_encontrados==1) {

                    $usuario_red_jarvis = $activos_jarvis[0]['usuario_red'];

                    //Si encuentra un usuario de red en Jarvis, compara con el de CXM si son diferentes deshabilitamos el usuario
                    if( $usuario_red_jarvis!==null && ($usuario_red_cxm !== $usuario_red_jarvis) ) {
                        $array_deshabilitar_usuarios[] = $usuario_no_admin;
                        continue;
                    }

                    $tipocargo_encargo = $activos_jarvis[0]['tipocargo_encargo'];
                    $tipocargo_principal = $activos_jarvis[0]['tipocargo_principal'];
                    $cargos_encargo = $activos_jarvis[0]['cargos_encargo'];
                    $nombre_completo_usua_jarvis = $activos_jarvis[0]['nombre_completo'];
                    $documento_usua_jarvis = $activos_jarvis[0]['documento']; 
                   
                } 


                if($cantidad_registros_encontrados > 1) {
                    $encontrado = false;
                    //recorro los resultados buscando el que coincida con CXM
                    foreach($activos_jarvis as $usuario){
                        $usuario_red_jarvis = $usuario['usuario_red'];

                        if($usuario_red_jarvis == $usuario_red_cxm){
                            $encontrado = true;                 
                        }
                    }                    

                    if(!$encontrado){
                        //Si no encontro el usuario de red de CXM en Jarvis deshabilitarlo y continuamos con la siguiente iteracion
                        $array_deshabilitar_usuarios[] = $usuario_no_admin;
                        continue;
                    }

                    if($encontrado){
                        $tipocargo_encargo = $usuario['tipocargo_encargo'];
                        $tipocargo_principal = $usuario['tipocargo_principal'];
                        $cargos_encargo = $usuario['cargos_encargo'];
                        $nombre_completo_usua_jarvis = $usuario['nombre_completo'];
                        $documento_usua_jarvis = $usuario['documento'];
                        
                    }
                }                

                //VALIDACION PARA HABILITAR USUARIOS TIPO DE CARGO = ADMINISTRATIVO ----------------------------------------------
                //Se habilita con es_administrativo = 1
                //Se deshabilita con es_administrativo = 0 y "no usar" en el nombre, usaurio_red y 5 ceros adelante del documento

                if ($tipocargo_encargo ==null && $tipocargo_principal =="Administrativo") {

                    if($es_administrativo=='1'){
                        continue;
                    }
                    $usuario_admin = ["id" => $id_usuario, "es_administrativo" => $es_administrativo, "nombre_completo_jarvis" => $nombre_completo_usua_jarvis, "documento_jarvis" => $documento_usua_jarvis];
                
                    //agregar en array para habilitar masivamente
                    $array_habilitar_usuarios[] = $usuario_admin;

                }

                if ($tipocargo_encargo =="Administrativo" && $tipocargo_principal =="Administrativo") {
                    
                    if($es_administrativo=='1'){
                        continue;
                    }
                    
                    $usuario_admin = ["id" => $id_usuario, "es_administrativo" => $es_administrativo, "nombre_completo_jarvis" => $nombre_completo_usua_jarvis, "documento_jarvis" => $documento_usua_jarvis];
                
                    //agregar en array para habilitar masivamente
                    $array_habilitar_usuarios[] = $usuario_admin; 
                   
                }

                if ($tipocargo_encargo =="Operativo" && $tipocargo_principal =="Administrativo") {

                    if($es_administrativo=='1'){
                        continue;
                    }
                    
                    $usuario_admin = ["id" => $id_usuario, "es_administrativo" => $es_administrativo, "nombre_completo_jarvis" => $nombre_completo_usua_jarvis, "documento_jarvis" => $documento_usua_jarvis];
                
                    //agregar en array para habilitar masivamente
                    $array_habilitar_usuarios[] = $usuario_admin; 
                   
                }

                //Validaciones para deshabilitar los que no son administrativos en CXM

                if ($tipocargo_encargo ==null && $tipocargo_principal =="Operativo") {                    
                    
                    if($cargos_encargo !=='0') {
                        $actualizar_encargo = Yii::$app->db->createCommand()->update('tbl_usuarios', [
                        'cargos_encargo' => null,
                        ],'usua_id ='.$id_usuario.'')->execute();                          
                    }                 

                    if($es_administrativo=='0'){
                        continue;
                    }
                    
                    $array_deshabilitar_usuarios[] = $usuario_no_admin; //update esAdmin 0 y colocar no usar en nombre
                    
                }
                
                if ($tipocargo_encargo =="Administrativo" && $tipocargo_principal =="Operativo") {
                    
                    if($es_administrativo=='0'){
                        //habilitar el usuario quitandole el "no usar"
                        $actualizar_encargo = Yii::$app->db->createCommand()->update('tbl_usuarios', [
                            'usua_nombre' => $nombre_completo_usua_jarvis,
                            'usua_usuario' => $usuario_red_jarvis,
                            'usua_identificacion' => $documento_usua_jarvis,
                            'es_administrativo'=> 1,
                            'cargos_encargo' => $cargos_encargo,
                            ],'usua_id ='.$id_usuario.'')->execute();
                        
                    } else {

                        $actualizar_encargo = Yii::$app->db->createCommand()->update('tbl_usuarios', [
                            'es_administrativo'=> 1,
                            'cargos_encargo' => $cargos_encargo,
                            ],'usua_id ='.$id_usuario.'')->execute();

                    }
                                            
                }

                if ($tipocargo_encargo =="Operativo" && $tipocargo_principal =="Operativo") {

                    if($es_administrativo=='0'){
                        continue;
                    }

                    $array_deshabilitar_usuarios[] = $usuario_no_admin;
                }
            }

            $this->deshabilitar_usuarios($array_deshabilitar_usuarios);
            $this->habilitar_usuarios($array_habilitar_usuarios);     
        }

        die(json_encode(array("status"=>"1","data"=>"Actualizacion exitosa para todos los lotes de registros")));

    }

    public function deshabilitar_usuarios($usuarios_no_administrativos) {

        //Registros que no necesitan agregar el NO USAR porque ya lo tienen
        $combinacionesBuscar = array('(no usar)', '(No usar)', '(NO USAR)', 'NO USAR.');

        // Eliminar elementos del array $usuarios_no_administrativos que ya contienen el "no usar"
        foreach ($usuarios_no_administrativos as $clave => $resultado) {

            $nombreCompleto = $resultado['nombre_completo']; 
            $usua_red_cxm = $resultado['usuario_red'];
            
            foreach ($combinacionesBuscar as $combinacion) {                

                if (stripos($nombreCompleto, $combinacion) !== false && stripos($usua_red_cxm, $combinacion)!== false) {        
                  unset($usuarios_no_administrativos[$clave]);
                  break; // Salir del bucle interno una vez que se encuentra una combinación
                }
            }
        }

        //Si aún tenemos usuarios que les falta el NO USAR 
        if(!empty($usuarios_no_administrativos)) {
            
            $idsCoincidentes = array_column($usuarios_no_administrativos, 'id');
            $cadena_idsCoincidentes =  "'" . implode("','", $idsCoincidentes) . "'";

            $comando = Yii::$app->db->createCommand('
            UPDATE tbl_usuarios
            SET usua_nombre = CONCAT("(no usar) ", usua_nombre),
            usua_usuario = CONCAT(usua_usuario, " (no usar)"),
            usua_identificacion = IF(usua_identificacion LIKE "000%", usua_identificacion, CONCAT("00000", usua_identificacion)),
            es_administrativo = 0
            WHERE usua_id IN (' . $cadena_idsCoincidentes . ')');
            $filas_afectadas = $comando->execute();

            if ($filas_afectadas > 0) {
                return 1; // Actualización exitosa
            } else {
                return 0; // Ocurrió un error en la actualizacion
            }

        }
        
    }

    public function habilitar_usuarios($usuarios_administrativos) {
       
        //Tomo los ids de los usuarios
        $ids_para_habilitar = array_column($usuarios_administrativos, 'id');
        $cadena_ids_para_habilitar =  "'" . implode("','", $ids_para_habilitar) . "'";  

        // Actualizar masivamente la columna 'esAdmin' = 1
        $comando = Yii::$app->db->createCommand('
        UPDATE tbl_usuarios
        SET es_administrativo = 1
        WHERE usua_id IN (' . $cadena_ids_para_habilitar . ')');

        $filas_afectadas = $comando->execute();

    }

}
                
?>
