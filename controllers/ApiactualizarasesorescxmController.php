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

class ApiactualizarasesorescxmController extends \yii\web\Controller {

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
                'actions' => ['apivalidaractivosjarvis'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apivalidaractivosjarvis'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    //Funcion que deshabilita o habilita segun el tipo de cargo a todos los asesores activos de la tabla tbl_evaluados 
    public function actionApivalidaractivosjarvis() {

        // Cantidad total de registros 
        $total_registros = (new Query())
        ->select(['COUNT(*)'])
        ->from('tbl_evaluados')
        ->where(['usua_activo' => 'S'])
        ->andWhere(['excepcion_jarvis' => '0'])
        ->scalar();

        $limite = 1000; // Número de registros por página
        $total_paginas = ceil($total_registros/$limite);
        
        for ($paginaActual = 1; $paginaActual <= $total_paginas; $paginaActual++) {

            // Calcular el offset
            $offset = ($paginaActual - 1) * $limite;

            // Traer los registros segun limit y offset
            $usuarios_asesores = (new Query()) 
            ->select(['ev.id', 'ev.name AS nombre_completo', 'ev.dsusuario_red AS usuario_red', 'ev.identificacion AS documento', 'ev.es_operativo'])
            ->from('tbl_evaluados ev')
            ->where(['ev.usua_activo' => 'S'])
            ->andWhere(['ev.excepcion_jarvis' => '0'])
            ->limit($limite)
            ->offset($offset)
            ->all();
            
            $array_deshabilitar_usuarios= [];
            $array_habilitar_usuarios= [];
            
            // Verificar si $usuarios_asesores es un array y si tiene al menos un elemento en el índice 0
            if (is_array($usuarios_asesores) && isset($usuarios_asesores[0]) ) {

                foreach ($usuarios_asesores as $info_usuario) {
                    $id_usuario = $info_usuario['id'];
                    $usuario_red_cxm = $info_usuario['usuario_red'];
                    $cc_usuario = $info_usuario['documento'];
                    $es_operativo = $info_usuario['es_operativo'];
                    $nombre_completo_usua_jarvis = "";
                    $documento_usua_jarvis = "";

                    $usuario_no_operativo = ["id" => $id_usuario, "nombre_completo" => $info_usuario['nombre_completo'], "usuario_red" => $info_usuario['usuario_red']];
                    $usuario_operativo = ["id" => $id_usuario, "es_operativo" => $es_operativo, "nombre_completo_jarvis" => $nombre_completo_usua_jarvis, "documento_jarvis" => $documento_usua_jarvis];

                    $paramsBuscaDocumento = [':documento'=>$cc_usuario];
                    $activos_jarvis = Yii::$app->dbjarvis->createCommand('SELECT
                    CONCAT(datos.primer_apellido, " ", datos.segundo_apellido, " ", datos.primer_nombre, " ", datos.segundo_nombre) AS nombre_completo,
                    LOWER(ured.usuario_red) AS usuario_red, dp.fecha_actual, dp.documento,
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
                        //No encontró registros en Jarvis: deshabilitar
                        $array_deshabilitar_usuarios[] = $usuario_no_operativo;
                        continue;         
                    }

                    if($cantidad_registros_encontrados==1) {

                        $usuario_red_jarvis = $activos_jarvis[0]['usuario_red'];

                        //Si encuentra un usuario de red en Jarvis, compara con el de CXM si son diferentes deshabilitamos el usuario
                        if( $usuario_red_jarvis!==null && ($usuario_red_cxm !== $usuario_red_jarvis) ) {                        
                            $array_deshabilitar_usuarios[] = $usuario_no_operativo;
                            continue;
                        }

                        //Si son los mismos usuarios de red o si no pudo compararlos, continua...
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
                            $array_deshabilitar_usuarios[] = $usuario_no_operativo;
                            continue;
                        }

                        if($encontrado){
                            $tipocargo_encargo = $usuario['tipocargo_encargo'];
                            $tipocargo_principal = $usuario['tipocargo_principal'];
                            $cargos_encargo = $usuario['cargos_encargo'];
                            $nombre_completo_usua_jarvis = $usuario['nombre_completo'];
                            $documento_usua_jarvis = $usuario['documento'];
                            $usuario_red_jarvis = $usuario['usuario_red'];
                        }
                    }             


                    //VALIDACION PARA HABILITAR USUARIOS TIPO DE CARGO = OPERATIVO ----------------------------------------------
                    //Se habilita con es_operativo = 1

                    if ($tipocargo_encargo ==null && $tipocargo_principal =="Operativo") {

                        if($es_operativo=='1'){
                            continue;
                        }
                        $usuario_operativo = ["id" => $id_usuario, "es_operativo" => $es_operativo, "nombre_completo_jarvis" => $nombre_completo_usua_jarvis, "documento_jarvis" => $documento_usua_jarvis];
                    
                        //agregar en array para habilitar masivamente
                        $array_habilitar_usuarios[] = $usuario_operativo;

                    }

                    if ($tipocargo_encargo =="Operativo" && $tipocargo_principal =="Operativo") {
                        
                        if($es_operativo=='1'){
                            continue;
                        }
                        
                        $usuario_operativo = ["id" => $id_usuario, "es_operativo" => $es_operativo, "nombre_completo_jarvis" => $nombre_completo_usua_jarvis, "documento_jarvis" => $documento_usua_jarvis];
                    
                        //agregar en array para habilitar masivamente
                        $array_habilitar_usuarios[] = $usuario_operativo;
                    
                    }

                    //Es operativo como cargo oficial, tambien debe estar habilitado como administrativo en tbl_usuarios               
                    if ($tipocargo_encargo =="Administrativo" && $tipocargo_principal =="Operativo") {                    

                        if($es_operativo=='1'){
                            continue;
                        }

                        $usuario_operativo = ["id" => $id_usuario, "es_operativo" => $es_operativo, "nombre_completo_jarvis" => $nombre_completo_usua_jarvis, "documento_jarvis" => $documento_usua_jarvis];
                    
                        //agregar en array para habilitar masivamente
                        $array_habilitar_usuarios[] = $usuario_operativo;
                    
                    }


                    //VALIDACION PARA DESHABILITAR A LOS QUE NO SON CARGO OPERATIVO ----------------------------------------------
                    if ($tipocargo_encargo ==null && $tipocargo_principal =="Administrativo") {                    
                        
                        if($es_operativo=='0') {
                            continue;
                        }
                        
                        $array_deshabilitar_usuarios[] = $usuario_no_operativo;
                        
                    }

                    if ($tipocargo_encargo =="Administrativo" && $tipocargo_principal =="Administrativo") {

                        if($es_operativo=='0'){
                            continue;
                        }

                        $array_deshabilitar_usuarios[] = $usuario_no_operativo;
                    }
                    

                    //HABILITAR TEMPORALMENTE MIENTRAS ESTA EN ENCARGO -----------------------------------------
                    if ($tipocargo_encargo =="Operativo" && $tipocargo_principal =="Administrativo") {
                        
                        if($es_operativo=='0'){
                            //habilitar el usuario quitandole el "no usar"
                            $actualizar_encargo = Yii::$app->db->createCommand()->update('tbl_evaluados', [
                                'name' => $nombre_completo_usua_jarvis,
                                'dsusuario_red'=> $usuario_red_jarvis,
                                'identificacion' => $documento_usua_jarvis,
                                'es_operativo'=> 1,
                                ],'usua_id ='.$id_usuario.'')->execute();
                            
                        } else {
                            $usuario_operativo = ["id" => $id_usuario, "es_operativo" => $es_operativo, "nombre_completo_jarvis" => $nombre_completo_usua_jarvis, "documento_jarvis" => $documento_usua_jarvis];

                            //agregar en array para habilitar masivamente
                            $array_habilitar_usuarios[] = $usuario_operativo;
                        }                                            
                    }
                    
                }

                $this->deshabilitar_usuarios($array_deshabilitar_usuarios);
                $this->habilitar_usuarios($array_habilitar_usuarios); 

            } else {
                // $usuarios_asesores no es un array válido o no tiene elementos en el índice 0
                die(json_encode(array("status"=>"0","data"=>"Error: No se puede acceder al elemento 0 del array usuarios_asesores")));
                                
            }
        }
        
        die(json_encode(array("status"=>"1","data"=>"Actualizacion exitosa para todos los lotes de registros")));
    }



    //Funcion que actualiza masivamente la columna es_operativo = 0 y les coloca no usar al nombre y cinco ceros antes del documento 
    public function deshabilitar_usuarios($usuarios_no_operativos) {
       
        if( !empty($usuarios_no_operativos) ) {

            //todos los usuarios que deben tener es_operativo=0
            $usuariosCoincidentes = array_column($usuarios_no_operativos, 'id');
            $cadena_usuariosCoincidentes =  "'" . implode("','", $usuariosCoincidentes) . "'";

            //Variaciones del "no usar" encontradas
            $combinacionesBuscar = array('(no usar)', '(No usar)', '(NO USAR)', 'NO USAR.');

            // Eliminar elementos del array $usuarios_no_operativos que ya contienen el "no usar"
            foreach ($usuarios_no_operativos as $clave => $resultado) {
                
                $nombreCompleto = $resultado['nombre_completo'];
                $usua_red_cxm = $resultado['usuario_red'];
                
                foreach ($combinacionesBuscar as $combinacion) {

                    if (stripos($nombreCompleto, $combinacion) !== false && stripos($usua_red_cxm, $combinacion)!== false ) {
                        unset($usuarios_no_operativos[$clave]); //Removemos el registro encontrado
                        break; // Salir del bucle interno una vez que se encuentra una combinación
                    }
                }
            }

            $idsCoincidentes = array_column($usuarios_no_operativos, 'id');
            $cadena_idsCoincidentes =  "'" . implode("','", $idsCoincidentes) . "'";  

            //Si aún tenemos usuarios que les falta el NO USAR se lo agregamos
            if(!empty($idsCoincidentes)) {

                // Actualizar con el "no usar"                
                $comando = Yii::$app->db->createCommand('
                UPDATE tbl_evaluados
                SET name = CONCAT("(no usar)", name),
                dsusuario_red = CONCAT(dsusuario_red, "(no usar)")              
                WHERE id IN (' . $cadena_idsCoincidentes . ')')->execute();            
            }

                // Actualizar la columna 'es_operativo' a '0' y agregarle 5 ceros adelante del documento
                $update_no_operativo = Yii::$app->db->createCommand('
                UPDATE tbl_evaluados
                SET es_operativo = 0,
                identificacion = IF(identificacion LIKE "000%", identificacion, CONCAT("00000", identificacion))                            
                WHERE id IN (' . $cadena_usuariosCoincidentes . ')')->execute();        
        }
     
    }

    //Funcion que actualiza masivamente la columna es_operativo = 1 
    public function habilitar_usuarios($usuarios_operativos) {
       
        if(!empty($usuarios_operativos)){
            //Tomo los ids de los usuarios
            $ids_para_habilitar = array_column($usuarios_operativos, 'id');
            $cadena_ids_para_habilitar =  "'" . implode("','", $ids_para_habilitar) . "'";  

            // Actualizar masivamente la columna 'es_operativo'
            $comando = Yii::$app->db->createCommand('
            UPDATE tbl_evaluados
            SET es_operativo = 1
            WHERE id IN (' . $cadena_ids_para_habilitar . ')');

            $filas_afectadas = $comando->execute();

            if ($filas_afectadas > 0) {
                return 1; // Actualización exitosa
            } else {
                return 0; // Ocurrió un error en la actualizacion
            }
        } 
    }
}
                
?>