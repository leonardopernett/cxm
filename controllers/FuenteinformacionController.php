<?php

namespace app\controllers;

use Yii;
use app\models\Usuarios;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsuariosController implements the CRUD actions for Usuarios model.
 */
class FuenteinformacionController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    $msg = \Yii::t('app', 'The requested Item could not be found.');
                    Yii::$app->session->setFlash('danger', $msg);
                    $url = \yii\helpers\Url::to(['site/index']);
                    return $action->controller->redirect($url);
                },
                        'rules' => [
                            [
                                'actions' => ['index', 'usuarios_valorado','nuevosdistribucion', 'exportarlider', 'exportarpcrc', 'exportarsinusuariored', 'exportarsinusuarioredlider', 'actualizacionequipos', 'actualizacionequipospcrc', 'exportarequiposactual','exportarvaloradonocxm','exportarlistanoactualcxm','exportarlista2','exportarformatovalorado','nuevosformatodistribucion'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
                            ],
                        ],
                    ],
                ];
            }

            /**
             * Lists all Usuarios models.
             * @return mixed
             */
            public function actionIndex() {
                $sessiones = Yii::$app->user->identity->id;

            return $this->render('index', [
                'sessiones' => $sessiones,
                ]);
            }

            public function actionUsuarios_valorado(){
                $sessiones = Yii::$app->user->identity->id;
                $txtanulado = 0;
                $txtfechacreacion = date("Y-m-d");
     
                Yii::$app->db->createCommand("truncate table tbl_tmpvaloradosdistribucion")->execute();
     
                $query = Yii::$app->get('dbjarvis')->createCommand("select `dg5`.`documento` AS `docJefe`, `dg5`.`nombre_completo` AS `jefe`, `e1`.usuario_red AS usuario_red_jefe,
                                    IFNULL(dp.documento, 'Sin información' COLLATE utf8_unicode_ci) AS documento, `dg`.`nombre_completo` AS `nombreEmpleado`, 
                                    `e`.usuario_red,
                                    IF(dp.cargos_encargo = 0, CONCAT_WS(' ', `po1`.`posicion`, fu1.funcion), CONCAT_WS(' ', `po2`.`posicion`, fu2.funcion)) AS cargo,  
                                    IF(dp.cod_pcrc != 0, `cl1`.`id_dp_clientes`, IF(dp.id_dp_centros_costos != 0, `cl2`.`id_dp_clientes`, IF(dp.id_dp_centros_costos_adm != 0, `dp`.`id_dp_centros_costos_adm`, 'Sin información' COLLATE utf8_unicode_ci))) AS codClienteArea, 
                                    IF(dp.cod_pcrc != 0, `cl1`.`cliente`, IF(dp.id_dp_centros_costos != 0, `cl2`.`cliente`, IF(dp.id_dp_centros_costos_adm != 0, `ar`.`area_general`, 'Sin información' COLLATE utf8_unicode_ci))) AS clienteArea,				    
                                    IF(dp.cod_pcrc != 0, `pc`.`cod_pcrc`, IF(dp.id_dp_centros_costos_adm != 0, `ad`.`id_dp_centros_costos_adm`, 'Sin información')) AS codCecoPcrc, 
                                    `pc`.`ciudad` AS `ciudadPcrc`, IFNULL(IF(dg.fecha_alta = '1900-01-01', 'Sin información', dg.fecha_alta), 'Sin información' COLLATE utf8_unicode_ci) AS fechaAlta, `es`.`tipo_estado` AS `estado`, `ua1`.email_corporativo AS `correo_corp`
                                    FROM `dp_distribucion_personal` AS `dp`
                                    LEFT JOIN `dp_datos_generales` AS `dg` ON `dg`.`documento` = `dp`.`documento`
                                    LEFT JOIN `dp_solicitudes_administrativas` AS `da` ON `da`.`id_dp_solicitudes_administrativas` = `dg`.`id_dp_solicitudes` AND `dg`.`id_tipo_solicitud` = '1'
                                    JOIN `dp_tipo_documento` AS `ti` ON `ti`.`id_dp_tipo_documento` = `dg`.`id_dp_tipo_documento`
                                    LEFT JOIN `dp_cargos` AS `dc1` ON `dc1`.`id_dp_cargos` = `dp`.`id_dp_cargos`
                                    LEFT JOIN `dp_posicion` AS `po1` ON `po1`.`id_dp_posicion` = `dc1`.`id_dp_posicion`
                                    LEFT JOIN `dp_funciones` AS `fu1` ON `fu1`.`id_dp_funciones` = `dc1`.`id_dp_funciones`
                                    LEFT JOIN `dp_cargos` AS `dc2` ON `dc2`.`id_dp_cargos` = `dp`.`cargos_encargo`
                                    LEFT JOIN `dp_posicion` AS `po2` ON `po2`.`id_dp_posicion` = `dc2`.`id_dp_posicion`
                                    LEFT JOIN `dp_funciones` AS `fu2` ON `fu2`.`id_dp_funciones` = `dc2`.`id_dp_funciones`
                                    LEFT JOIN `dp_estados` AS `es` ON `es`.`id_dp_estados` = `dp`.`id_dp_estados`
                                    LEFT JOIN `dp_pcrc` AS `pc1` ON `pc1`.`cod_pcrc` = `dp`.`cod_pcrc`
                                    LEFT JOIN `dp_clientes` AS `cl1` ON `cl1`.`id_dp_clientes` = `pc1`.`id_dp_clientes`
                                    LEFT JOIN `dp_centros_costos` AS `cc1` ON `cc1`.`id_dp_centros_costos` = `dp`.`id_dp_centros_costos`
                                    LEFT JOIN `dp_clientes` AS `cl2` ON `cl2`.`id_dp_clientes` = `cc1`.`id_dp_clientes`
                                    LEFT JOIN `dp_centros_costos_adm` AS `ad` ON `ad`.`id_dp_centros_costos_adm` = `dp`.`id_dp_centros_costos_adm`
                                    LEFT JOIN `dp_centros_admin_area` AS `ar` ON `ar`.`id_dp_centros_admin_area` = `ad`.`id_dp_centros_admin_area`
                                    LEFT JOIN `dp_datos_generales` AS `dg1` ON `dg1`.`documento` = `ar`.`documento`
                                    LEFT JOIN `dp_centros_costos` AS `cc` ON `cc`.`id_dp_centros_costos` = `dp`.`id_dp_centros_costos`
                                    LEFT JOIN `dp_programa` AS `pr` ON `pr`.`id_dp_centros_costos` = `cc`.`id_dp_centros_costos`
                                    LEFT JOIN `dp_pcrc` AS `pc` ON `pc`.`cod_pcrc` = `dp`.`cod_pcrc`
                                    LEFT JOIN `dp_datos_generales` AS `dg2` ON `dg2`.`documento` = `cc`.`documento_gerente`
                                    LEFT JOIN `dp_datos_generales` AS `dg3` ON `dg3`.`documento` = `cc`.`documento_director`
                                    LEFT JOIN `dp_distribucion_personal` AS `dp2` ON `dp2`.`documento` = `dp`.`documento_jefe` AND `dp2`.`fecha_actual` = `dp`.`fecha_actual` AND `dp`.`id_dp_cargos` IN(39322,18190,40323, 40324)
                                    LEFT JOIN `dp_distribucion_personal` AS `dp3` ON `dp3`.`documento` = `dp2`.`documento_jefe` AND `dp3`.`fecha_actual` = `dp`.`fecha_actual`
                                    LEFT JOIN `dp_datos_generales` AS `dg4` ON `dg4`.`documento` = `dp3`.`documento`
                                    LEFT JOIN `dp_datos_generales` AS `dg5` ON `dg5`.`documento` = `dp`.`documento_jefe`
                                    LEFT JOIN `dp_historial_cambios` AS `dh` ON `dh`.`documento` = `dg`.`documento` AND `dh`.`id_dp_historial_tipo_cambios` = 15
                                    LEFT JOIN `dp_historial_cambios` AS `dh2` ON `dh2`.`documento` = `dg`.`documento` AND `dh2`.`id_dp_historial_tipo_cambios` = 48
                                    LEFT JOIN `dp_usuarios_red` AS `e`  ON `e`.`documento` = `dp`.`documento`
                                    LEFT JOIN `dp_usuarios_red` AS `e1`  ON `e1`.`documento` = `dg5`.`documento`
				    LEFT JOIN `dp_actualizacion_datos` AS `ua1`  ON `ua1`.`documento` = `dp`.`documento`
                                    WHERE `dp`.`fecha_actual` = (SELECT config.valor FROM jarvis_configuracion_general as config WHERE config.nombre = 'mes_activo_dp' ) 
                                    AND dp.id_dp_cargos IN (39322,18190,39909)
                                    AND dp.id_dp_estados NOT IN (305,306,309,310,317,319,327)                                    
			            AND e.fecha_creacion_usuario = ( SELECT MAX(aa.fecha_creacion_usuario) FROM dp_usuarios_red aa WHERE aa.documento = dp.documento )                
                                    GROUP BY `dp`.`documento` COLLATE 'utf8mb4_unicode_520_ci'")->queryAll();
     
                foreach ($query as $key => $value) {
     
                    Yii::$app->db->createCommand()->insert('tbl_tmpvaloradosdistribucion',[
                                                             'documento' => $value['documento'],
                                                             'nombreempleado' => $value['nombreEmpleado'],
                                                             'usuario_red' => $value['usuario_red'],
                                                             'documentojefe' => $value['docJefe'],
                                                             'nombrejefe' => $value['jefe'],
                                                             'usuario_redjefe' => $value['usuario_red_jefe'],
                                                             'cargo' => $value['cargo'],
                                                             'codpcrc' => $value['codCecoPcrc'],
                                                             'idpcrc' => $value['codClienteArea'],
                                                             'nombrepcrc' => $value['clienteArea'],
                                                             'ciudad' => $value['ciudadPcrc'],
                                                             'fechaalta' => $value['fechaAlta'],
                                                             'estado'  => $value['estado'],
                                                             'anulado' => $txtanulado,
                                                             'fechacreacion' => $txtfechacreacion,
                                                             'usua_id' => $sessiones,
							     'correo' => $value['correo_corp'],
                                                         ])->execute();
     
                }
     
                return $this->redirect('index');
		  
     
            }

            public function actionNuevosdistribucion(){
                $sessiones = Yii::$app->user->identity->id;
                $txtanulado = 0;
                $txtfechacreacion = date("Y-m-d");
                // borrado de tabla temporal
                Yii::$app->db->createCommand("truncate table tbl_tmpdistribucionlidernuevo")->execute();

                $varlistalider = Yii::$app->db->createCommand("SELECT  v.documentojefe, v.nombrejefe, v.usuario_redjefe from tbl_tmpvaloradosdistribucion v  WHERE v.cargo = 'Representante De Servicio' GROUP BY v.documentojefe")->queryAll();

                $varlistalidernocx = Yii::$app->db->createCommand("SELECT lista.documentojefe, lista.nombrejefe, lista.usuario_redjefe, lista.idpcrc, lista.nombrepcrc from (SELECT  v.documentojefe, v.nombrejefe, v.usuario_redjefe, v.idpcrc, v.nombrepcrc from tbl_tmpvaloradosdistribucion v
                                                    WHERE v.cargo = 'Representante De Servicio'
                                                    GROUP BY v.documentojefe) lista
                                                    WHERE lista.documentojefe NOT IN(SELECT u.usua_identificacion FROM tbl_usuarios u)  AND lista.usuario_redjefe IS NOT null
                                                    ORDER BY lista.documentojefe desc")->queryAll();
                
                foreach ($varlistalidernocx as $key => $value) {
                    Yii::$app->db->createCommand()->insert('tbl_tmpdistribucionlidernuevo',[
                        
                        'documentojefe' => $value['documentojefe'],
                        'nombrejefe' => $value['nombrejefe'],
                        'usuario_redjefe' => $value['usuario_redjefe'],
                        'idpcrc' => $value['idpcrc'],
                        'nombrepcrc' => $value['nombrepcrc'],
                        'anulado' => $txtanulado,
                        'fechacreacion' => $txtfechacreacion,
                        'usua_id' => $sessiones,
                    ])->execute();
                }

                $varlistalidernuevo = Yii::$app->db->createCommand("SELECT t.documentojefe, t.nombrejefe, t.usuario_redjefe, t.idpcrc, t.nombrepcrc from tbl_tmpdistribucionlidernuevo t ORDER BY t.idlidernuevo")->queryAll();

                $varestado = 'D';
                $varactivo = 'S';
                foreach ($varlistalidernuevo as $key => $value) {
                    $varemail = $value['usuario_redjefe'].'@grupokonecta.com';
                    $varbuscar = $value['nombrepcrc'];
                    if ($varbuscar != 'Vodafone Ono Sau' && $varbuscar != 'Enel Chile' && $varbuscar != 'Konecta BTO' && $varbuscar != 'Centro de mensajería') {
                    
                        Yii::$app->db->createCommand()->insert('tbl_usuarios',[
                            'usua_usuario' => $value['usuario_redjefe'],
                            'usua_nombre' => $value['nombrejefe'],
                            'usua_email' =>$varemail,
                            'usua_identificacion' => $value['documentojefe'],
                            'usua_activo' => $varactivo,                                                  
                            'usua_estado' => $varestado,
                            'fechacreacion' => $txtfechacreacion,
                        ])->execute();                          
                    }
                }
                $varrolid = 273;


                foreach ($varlistalidernuevo as $key => $value) {
                    $varbuscar = $value['nombrepcrc'];
		    $vardocumento1 = $value['documentojefe'];
                    if ($varbuscar != 'Vodafone Ono Sau' && $varbuscar != 'Enel Chile' && $varbuscar != 'Konecta BTO' && $varbuscar != 'Centro de mensajería') {
                        
                        if ($varbuscar == 'Suramericana') {
                            $varbuscar = 'Sura';
                        }
                        if ($varbuscar == 'Directv Puerto Rico') {
                            $varbuscar = 'DTV PTO RICO LIDER';
                        }
                        if ($varbuscar == 'DirecTV Argentina') {
                            $varbuscar = 'DTV TVT ARG - COL LIDER';
                        }
                        if ($varbuscar == 'Direc TV Colombia') {
                            $varbuscar = 'DTV COL LIDER';
                        }      
                        if ($varbuscar == 'Bancoolombia') {
                            $varbuscar = 'DTV COL LIDER';
                        }                        
                        if ($varbuscar == 'Sufi') {
                            $varbuscar = 'Lider de equipo Sufi CBZ';
                        } 


                        $vargrupo_id = Yii::$app->db->createCommand("SELECT grupos_id from tbl_grupos_usuarios g WHERE g.grupo_descripcion LIKE '%$varbuscar%'")->queryScalar();

                        $varidusua = Yii::$app->db->createCommand("select max(usua_id) from tbl_usuarios where usua_identificacion = $vardocumento1")->queryScalar();
            
                        Yii::$app->db->createCommand()->insert('rel_grupos_usuarios',[
                                            'usuario_id' => intval($varidusua),
                                            'grupo_id' => $vargrupo_id,
                                        ])->execute();
                        Yii::$app->db->createCommand()->insert('rel_usuarios_roles',[
                                            'rel_usua_id' => intval($varidusua),
                                            'rel_role_id' => $varrolid,
                        ])->execute(); 
                    }
                }

	       //crear equipos
                $varverde = 1;
                $varamarillo = 1;
                foreach ($varlistalidernuevo as $key => $value) {
                    $varbuscar = $value['nombrepcrc'];
                    $varnombre = $value['nombrejefe'];
                    $vardocumento1 = $value['documentojefe'];
                    if ($varbuscar != 'Vodafone Ono Sau' && $varbuscar != 'Enel Chile' && $varbuscar != 'Konecta BTO' && $varbuscar != 'Centro de mensajería') {
                        
                        $varnombreequipo = $varnombre.'_'.$varbuscar;
                        $varidusua = Yii::$app->db->createCommand("select usua_id from tbl_usuarios where usua_identificacion = $vardocumento1")->queryScalar();
                        Yii::$app->db->createCommand()->insert('tbl_equipos',[
                            'name' => $varnombreequipo,
                            'nmumbral_verde' => $varverde,
                            'nmumbral_amarillo' => $varamarillo,
                            'usua_id' => $varidusua,
                        ])->execute();

                    }
                }

		$varlistalidernuevo = Yii::$app->db->createCommand("SELECT v.documento, v.usua_red, u.usua_id usua_id, v.pcrc nombrepcrc, v.nombrejefe nombrejefe from (SELECT t.documentojefe documento, t.usuario_redjefe usua_red, t.nombrepcrc pcrc, t.nombrejefe nombrejefe FROM tbl_tmpvaloradosdistribucion t 
                                                                    GROUP BY t.documentojefe) v
                                                                    INNER JOIN tbl_usuarios u ON v.usua_red = u.usua_usuario
                                                                    ORDER BY u.usua_id")->queryAll();


		//crear equipos 2
                $varverde = 1;
                $varamarillo = 1;
                foreach ($varlistalidernuevo as $key => $value) {
                    $varbuscar = $value['nombrepcrc'];
                    $varnombre = $value['nombrejefe'];
                    $varusua_id = $value['usua_id'];
                    if ($varbuscar != 'Vodafone Ono Sau' && $varbuscar != 'Enel Chile' && $varbuscar != 'Konecta BTO' && $varbuscar != 'Centro de mensajería') {
                        
                        $varnombreequipo = $varnombre.'_'.$varbuscar;
                        $varidusua = Yii::$app->db->createCommand("SELECT * FROM tbl_equipos e where e.usua_id =  $varusua_id")->queryScalar();
                        if (!$varidusua) {
                            Yii::$app->db->createCommand()->insert('tbl_equipos',[
                                'name' => $varnombreequipo,
                                'nmumbral_verde' => $varverde,
                                'nmumbral_amarillo' => $varamarillo,
                                'usua_id' => $varusua_id,
                            ])->execute();

                        }

                    }
                }


		//crear evaluados
                
                $varlistaevaluadonuevo = Yii::$app->db->createCommand("SELECT lista.documento, lista.nombreempleado, lista.usuario_red, lista.idpcrc from
                                                        (SELECT t.documento, t.nombreempleado, t.usuario_red, t.idpcrc, t.nombrepcrc
                                                        FROM tbl_tmpvaloradosdistribucion t
                                                        WHERE t.cargo = 'Representante De Servicio' AND t.usuario_red IS NOT NULL AND 
                                                        t.nombrepcrc NOT IN ('Vodafone Ono Sau','Enel Chile','Konecta BTO','Centro de mensajería')) lista
                                                        WHERE lista.documento NOT IN (SELECT e.identificacion FROM tbl_evaluados e)")->queryAll();
                
                foreach ($varlistaevaluadonuevo as $key => $value) {
                     
                        Yii::$app->db->createCommand()->insert('tbl_evaluados',[
                            'name' => $value['nombreempleado'],
                            'dsusuario_red' => $value['usuario_red'],
                            'identificacion' => $value['documento'],
                            'idpcrc' => $value['idpcrc'],
                            'usua_id' => $sessiones,
                            'fechacreacion' => $txtfechacreacion,
                        ])->execute();

                    }
                return $this->redirect('index');
            }

	    public function actionExportarlider(){
                $varlistalideres = Yii::$app->db->createCommand("SELECT d.documentojefe, d.nombrejefe, d.usuario_redjefe, d.idpcrc, d.nombrepcrc, d.fechacreacion FROM tbl_tmpdistribucionlidernuevo d")->queryAll();
              
                return $this->renderAjax('exportarlider',[
                  'varlistalideres' => $varlistalideres,
                  ]);
              }
	    
	    public function actionExportarpcrc(){
                $varlistalipcrc = Yii::$app->db->createCommand("SELECT distinct d.idpcrc, d.nombrepcrc from tbl_tmpdistribucionlidernuevo d WHERE d.nombrepcrc NOT IN (SELECT a.name FROM tbl_arbols a)")->queryAll();
              
                return $this->renderAjax('exportarpcrc',[
                  'varlistalipcrc' => $varlistalipcrc,
                  ]);
              }

	    public function actionExportarsinusuariored(){
                
                // listados de valorados sin usarios de red 
                $varlistalisinusuared = Yii::$app->db->createCommand("SELECT documento, nombreempleado, cargo FROM tbl_tmpvaloradosdistribucion WHERE usuario_red Is null")->queryAll();
              
                return $this->renderAjax('exportarsinusuariored',[
                  'varlistalisinusuared' => $varlistalisinusuared,
                  ]);
              }

	    public function actionExportarsinusuarioredlider(){
                
                // listados de valorados sin usarios de red 
                $varlistalisinusuaredlider = Yii::$app->db->createCommand("SELECT documentojefe, nombrejefe FROM tbl_tmpvaloradosdistribucion WHERE usuario_redjefe Is NULL")->queryAll();
              
                return $this->renderAjax('exportarsinusuarioredlider',[
                  'varlistalisinusuaredlider' => $varlistalisinusuaredlider,
                  ]);
              }

	     public function actionActualizacionequipos(){
               
                $varlistaequipos = Yii::$app->db->createCommand("SELECT  v.documentojefe, v.nombrejefe, v.usuario_redjefe, v.nombrepcrc 
                                                        from tbl_tmpvaloradosdistribucion v
                                                        WHERE v.cargo = 'Representante De Servicio' AND v.usuario_red IS NOT NULL AND 
                                                        v.nombrepcrc NOT IN ('Vodafone Ono Sau','Enel Chile','Konecta BTO','Centro de mensajería')
                                                        GROUP BY v.documentojefe, v.nombrepcrc")->queryAll();
                
                foreach ($varlistaequipos as $key => $value) {
                    $vardocumentojefe = $value['documentojefe'];               
                    $varidlider = Yii::$app->db->createCommand("SELECT max(usua_id) FROM tbl_usuarios  WHERE usua_identificacion = $vardocumentojefe")->queryScalar();
                    if($varidlider) {
                        $varidliderequipo = Yii::$app->db->createCommand("SELECT max(id) FROM tbl_equipos  WHERE usua_id = $varidlider AND NAME not LIKE '%no usar%'")->queryScalar();
                        if($varidliderequipo) {
                            $varlistaasesores = Yii::$app->db->createCommand("SELECT documento, usuario_red FROM tbl_tmpvaloradosdistribucion  WHERE documentojefe = $vardocumentojefe")->queryAll();   
                            $variddeleteequipo = Yii::$app->db->createCommand("delete from tbl_equipos_evaluados where equipo_id = $varidliderequipo")->execute();
                            
                            foreach ($varlistaasesores as $key => $value) {
                                $vardocumento = $value['documento'];
				$varusuariored = $value['usuario_red'];               
                                $varidasesor = Yii::$app->db->createCommand("SELECT id FROM tbl_evaluados   WHERE identificacion = $vardocumento AND dsusuario_red = '$varusuariored'")->queryScalar();  
                                if ($varidasesor != 0) {
                                    Yii::$app->db->createCommand()->insert('tbl_equipos_evaluados',[
                                        'evaluado_id' => $varidasesor,
                                        'equipo_id' => $varidliderequipo,                            
                                    ])->execute();
                                }

                            }
                        }
                    }
                }              
                return $this->redirect('index');
              }

              public function actionActualizacionequipospcrc(){
               
                $varlistaequipos = Yii::$app->db->createCommand("SELECT  v.documentojefe, v.nombrejefe, v.usuario_redjefe, v.nombrepcrc 
                                                        from tbl_tmpvaloradosdistribucion v
                                                        WHERE v.cargo = 'Representante De Servicio' AND v.usuario_red IS NOT NULL AND 
                                                        v.nombrepcrc NOT IN ('Vodafone Ono Sau','Enel Chile','Konecta BTO','Centro de mensajería')
                                                        GROUP BY v.documentojefe")->queryAll();
                
                foreach ($varlistaequipos as $key => $value) {
                    $vardocumentojefe = $value['documentojefe'];
                    $varnombrepcrc =  $value['nombrepcrc'];              
                    $varidlider = Yii::$app->db->createCommand("SELECT usua_id FROM tbl_usuarios  WHERE usua_identificacion = $vardocumentojefe")->queryScalar();
                    if($varidlider) {
                        $varidequipo = Yii::$app->db->createCommand("SELECT id FROM tbl_equipos  WHERE usua_id = $varidlider")->queryScalar();
                        $varidarbol = Yii::$app->db->createCommand("SELECT id FROM tbl_arbols  WHERE name = '$varnombrepcrc'")->queryScalar();
                        if($varidarbol){
                            if ($varidequipo) {
                                $varlistaidarbol = Yii::$app->db->createCommand("SELECT id FROM tbl_arbols WHERE arbol_id = $varidarbol")->queryAll();   
                                
                                foreach ($varlistaidarbol as $key => $value) {
                                    $varidarbols = $value['id'];                                       
                                    $varcanti = Yii::$app->db->createCommand("SELECT COUNT(id) FROM tbl_arbols_equipos  WHERE arbol_id = $varidarbols AND equipo_id = $varidequipo")->queryScalar();  
                                    if($varcanti == 0) {
                                        Yii::$app->db->createCommand()->insert('tbl_arbols_equipos',[
                                            'arbol_id' => $varidarbols,
                                            'equipo_id' => $varidequipo,                            
                                        ])->execute();
                                    }

                                }
                            }
                        }
                    }
                }
              
                return $this->redirect('index');
              }

	     public function actionExportarequiposactual(){
                
                // listados de valorados sin usarios de red 
                $varlistaequiposactual = Yii::$app->db->createCommand("SELECT e.name, u.usua_identificacion, ep.identificacion FROM tbl_equipos e 
                                                        INNER JOIN tbl_equipos_evaluados ee ON
                                                        e.id = ee.equipo_id
                                                        INNER JOIN tbl_evaluados ep ON 
                                                        ee.evaluado_id = ep.id
                                                        INNER JOIN tbl_usuarios u ON
                                                        e.usua_id = u.usua_id")->queryAll();
              
                return $this->renderAjax('exportarequiposactual',[
                  'varlistaequiposactual' => $varlistaequiposactual,
                  ]);
              }

      public function actionExportarlista2(){
                $varCorreo = Yii::$app->request->get("var_Destino");
        
                $varlistaequiposactual = Yii::$app->db->createCommand("SELECT e.name, u.usua_identificacion, ep.identificacion FROM tbl_equipos e 
                                                        INNER JOIN tbl_equipos_evaluados ee ON
                                                        e.id = ee.equipo_id
                                                        INNER JOIN tbl_evaluados ep ON 
                                                        ee.evaluado_id = ep.id
                                                        INNER JOIN tbl_usuarios u ON
                                                        e.usua_id = u.usua_id")->queryAll();

                $phpExc = new \PHPExcel();
                $phpExc->getProperties()
                        ->setCreator("Konecta")
                        ->setLastModifiedBy("Konecta")
                        ->setTitle("Lista de equipos - Fuentes de Información")
                        ->setSubject("Fuentes de Información")
                        ->setDescription("Este archivo contiene el listado de los equipos registrados")
                        ->setKeywords("Lista de equipos");
                $phpExc->setActiveSheetIndex(0);
        
                $phpExc->getActiveSheet()->setShowGridlines(False);
        
                $styleArray = array(
                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    );
        
        
                $styleColor = array( 
                        'fill' => array( 
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                            'color' => array('rgb' => '28559B'),
                        )
                    );
        
                $styleArrayTitle = array(
                        'font' => array(
                          'bold' => false,
                          'color' => array('rgb' => 'FFFFFF')
                        )
                    );
        
        
                $styleArraySubTitle2 = array(              
                        'fill' => array( 
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                            'color' => array('rgb' => 'C6C6C6'),
                        )
                    );  
        
                // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
                $styleArrayBody = array(
                        'font' => array(
                            'bold' => false,
                            'color' => array('rgb' => '2F4F4F')
                        ),
                        'borders' => array(
                            'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => 'DDDDDD')
                            )
                        )
                    );
        
        
                $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);
        
                $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
                $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
                $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
                $phpExc->setActiveSheetIndex(0)->mergeCells('A1:H1');
        
                $phpExc->getActiveSheet()->SetCellValue('A2','EQUIPO');
                $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
                $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);
        
                $phpExc->getActiveSheet()->SetCellValue('B2','IDENTIFICACION LIDER');
                $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
                $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);
        
                $phpExc->getActiveSheet()->SetCellValue('C2','IDENTIFICACION ASESOR');
                $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
                $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);        
                
                $numCell = 2;
                foreach ($varlistaequiposactual as $key => $value) {
                  $numCell++;
        
                  $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['name']); 
                  $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['usua_identificacion']); 
                  $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['identificacion']);
        
                }
                $numCell = $numCell;
        
        
        
                $hoy = getdate();
                $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoEquipos_Fuentes_informacion";
                      
                $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                        
                $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
                $tmpFile.= ".xls";
        
                $objWriter->save($tmpFile);
        
                $message = "<html><body>";
                $message .= "<h3>Adjunto del archivo tipo listado de los equipos en la fuente de informacion</h3>";
                $message .= "</body></html>";
        
                Yii::$app->mailer->compose()
                                ->setTo($varCorreo)
                                ->setFrom(Yii::$app->params['email_satu_from'])
                                ->setSubject("Envio Listado de equipos - Fuentes de informacion")
                                ->attach($tmpFile)
                                ->setHtmlBody($message)
                                ->send();
        
                $rtaenvio = 1;
                die(json_encode($rtaenvio));
        
              }

	public function actionExportarvaloradonocxm(){
                
                // listados de valorados que o estas en jarvis
                $varlistavaloradosjarvis = Yii::$app->db->createCommand("SELECT e.name, e.dsusuario_red, e.identificacion FROM tbl_tmpvaloradosdistribucion  t   
                                                        inner JOIN  tbl_evaluados e ON 
                                                        t.usuario_red = e.dsusuario_red")->queryAll();

                return $this->renderAjax('exportarvaloradonocxm',[
                  'varlistavaloradosjarvis ' => $varlistavaloradosjarvis ,
                  ]);
              }

              public function actionExportarlistanoactualcxm(){
                $varCorreo = Yii::$app->request->get("var_Destino");
                $varestatus = 1;
                $varlistavaloradosjarvis = Yii::$app->db->createCommand("SELECT e.name, e.dsusuario_red, e.identificacion FROM tbl_tmpvaloradosdistribucion  t   
                                                        inner JOIN  tbl_evaluados e ON 
                                                        t.usuario_red = e.dsusuario_red")->queryAll();
                foreach ($varlistavaloradosjarvis as $key => $value) {
                    $vardocumento = $value['identificacion'];               
                    
                    Yii::$app->db->createCommand()->update('tbl_evaluados',[
                        'estatus' => $varestatus,
                    ],'identificacion ='.$vardocumento.'')->execute();
                }
                //Se buscan los que tienen estatus 0
                $varlistavaloradocxmnojarvis = Yii::$app->db->createCommand("SELECT NAME, identificacion, dsusuario_red FROM tbl_evaluados  WHERE estatus = 0")->queryAll();
 
                $phpExc = new \PHPExcel();
                $phpExc->getProperties()
                        ->setCreator("Konecta")
                        ->setLastModifiedBy("Konecta")
                        ->setTitle("Lista de valorados - Fuentes de Información")
                        ->setSubject("Fuentes de Información")
                        ->setDescription("Este archivo contiene el listado de Valorados de CXM no encontrados en Jarvis ")
                        ->setKeywords("Lista de Valorados");
                $phpExc->setActiveSheetIndex(0);
        
                $phpExc->getActiveSheet()->setShowGridlines(False);
        
                $styleArray = array(
                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        ),
                    );
        
                $styleColor = array( 
                        'fill' => array( 
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                            'color' => array('rgb' => '28559B'),
                        )
                    );
        
                $styleArrayTitle = array(
                        'font' => array(
                          'bold' => false,
                          'color' => array('rgb' => 'FFFFFF')
                        )
                    );
        
        
                $styleArraySubTitle2 = array(              
                        'fill' => array( 
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                            'color' => array('rgb' => 'C6C6C6'),
                        )
                    );  
        
                // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
                $styleArrayBody = array(
                        'font' => array(
                            'bold' => false,
                            'color' => array('rgb' => '2F4F4F')
                        ),
                        'borders' => array(
                            'allborders' => array(
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => 'DDDDDD')
                            )
                        )
                    );
        
        
                $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);
        
                $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
                $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
                $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
                $phpExc->setActiveSheetIndex(0)->mergeCells('A1:H1');
        
                $phpExc->getActiveSheet()->SetCellValue('A2','NOMBRE');
                $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
                $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);
        
                $phpExc->getActiveSheet()->SetCellValue('B2','IDENTIFICACION');
                $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
                $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);
        
                $phpExc->getActiveSheet()->SetCellValue('C2','USUARIO DE RED');
                $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
                $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);        
                
                $numCell = 2;
                foreach ($varlistavaloradocxmnojarvis as $key => $value) {
                  $numCell++;
        
                  $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['name']); 
                  $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['identificacion']); 
                  $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['dsusuario_red']);
        
                }
                $numCell = $numCell;
        
        
        
                $hoy = getdate();
                $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoEvaluadoCXM_no_encontradoJarvis";
                      
                $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                        
                $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
                $tmpFile.= ".xls";
        
                $objWriter->save($tmpFile);
        
                $message = "<html><body>";
                $message .= "<h3>Adjunto del archivo tipo listado de los evaluados de CXM no encontrados en Jarvis en la fuente de informacion</h3>";
                $message .= "</body></html>";
        
                Yii::$app->mailer->compose()
                                ->setTo($varCorreo)
                                ->setFrom(Yii::$app->params['email_satu_from'])
                                ->setSubject("Envio Listado de Evaluados - Fuentes de informacion")
                                ->attach($tmpFile)
                                ->setHtmlBody($message)
                                ->send();
        
                $rtaenvio = 1;
                die(json_encode($rtaenvio));
        
              }

	      public function actionExportarformatovalorado(){
                $sessiones = Yii::$app->user->identity->id;
              
                return $this->renderAjax('exportarformatovalorado',[
                  'varsessiones' => $sessiones,
                  ]);
              }

	      public function actionNuevosformatodistribucion(){
                $model = new UploadForm2();
                $txtfechacreacion = date("Y-m-d");
                $sessiones = Yii::$app->user->identity->id;
            
                  if (Yii::$app->request->isPost) {
                      $model->file = UploadedFile::getInstance($model, 'file');
            
                      if ($model->file && $model->validate()) {                
                          $model->file->saveAs('categorias/' . $model->file->baseName . '.' . $model->file->extension);
                          
            
                          $fila = 1;
                          if (($gestor = fopen('categorias/' . $model->file->baseName . '.' . $model->file->extension, "r")) !== false) {
                            while (($datos = fgetcsv($gestor)) !== false) {
                              $numero = count($datos);
                          $fila++;
                          $valicedula = 0;
                          for ($c=0; $c < $numero; $c++) {
                              $varArray = $datos[$c]; 
                              $varDatos = explode(";", utf8_encode($varArray));
            
                                // inicio del importe                        
                                                            
                                $sessiones = Yii::$app->user->identity->id;
                                                
                                $txtanulado = 0;
                                $txtfechacreacion = date("Y-m-d");
                                

                                $varlidernuevo = Yii::$app->db->createCommand("SELECT COUNT(usua_identificacion) FROM tbl_usuarios  WHERE usua_identificacion = $varDatos[6] ")->queryScalar();

                                if($varlidernuevo == 0){
                                    $varestado = 'D';
                                    $varactivo = 'S';
                                    if($varDatos[6] != $valicedula) {
                                        $valicedula = $varDatos[6];
                                //insertar lideres en la tabla usuarios
                                            Yii::$app->db->createCommand()->insert('tbl_usuarios',[
                                                'usua_usuario' => $varDatos[5],
                                                'usua_nombre' => $varDatos[8],
                                                'usua_email' => $varDatos[9],
                                                'usua_identificacion' => $varDatos[6],
                                                'usua_activo' => $varactivo,                                                  
                                                'usua_estado' => $varestado,
                                                'fechacreacion' => $txtfechacreacion,
                                            ])->execute();                          
                                    }
                                }    
                            }
                        $varrolid = 273;
                        $valicedula = 0;
                        
                            for ($c=0; $c < $numero; $c++) {
                                $varArray = $datos[$c]; 
                                $varDatos = explode(";", utf8_encode($varArray));
                                $varbuscar = $varDatos[3];
                                $vardocumento1 = $varDatos[6];
                                $varlidernuevo = Yii::$app->db->createCommand("SELECT COUNT(usua_identificacion) FROM tbl_usuarios  WHERE usua_identificacion = $varDatos[6] ")->queryScalar();

                                if($varlidernuevo == 0){
                                    if($varDatos[6] != $valicedula) {
                                        $valicedula = $varDatos[6];
                                        
                                        $vargrupo_id = Yii::$app->db->createCommand("SELECT grupos_id from tbl_grupos_usuarios g WHERE g.grupo_descripcion LIKE '%$varbuscar%'")->queryScalar();

                                        $varidusua = Yii::$app->db->createCommand("select usua_id from tbl_usuarios where usua_identificacion = $vardocumento1")->queryScalar();

                                        Yii::$app->db->createCommand()->insert('rel_grupos_usuarios',[
                                                            'usuario_id' => intval($varidusua),
                                                            'grupo_id' => $vargrupo_id,
                                                        ])->execute();
                                        Yii::$app->db->createCommand()->insert('rel_usuarios_roles',[
                                                            'rel_usua_id' => intval($varidusua),
                                                            'rel_role_id' => $varrolid,
                                        ])->execute(); 
                                    }
                                }
                            }
                        
                            //fin insertar lideres

                            //crear equipos

                            $valicedula = 0;
                            $varverde = 1;
                            $varamarillo = 1;             
                            for ($c=0; $c < $numero; $c++) {
                                $varArray = $datos[$c]; 
                                $varDatos = explode(";", utf8_encode($varArray));
                                $varbuscar = $varDatos[3];
                                $vardocumento1 = $varDatos[6];
                                $varnombre = $varDatos[8];
                                $varlidernuevo = Yii::$app->db->createCommand("SELECT COUNT(usua_identificacion) FROM tbl_usuarios  WHERE usua_identificacion = $varDatos[6] ")->queryScalar();

                                if($varlidernuevo == 0){
                                    if($varDatos[6] != $valicedula) {
                                        $valicedula = $varDatos[6];
                                
                                        $varnombreequipo = $varnombre.'_'.$varbuscar;
                                        $varidusua = Yii::$app->db->createCommand("select usua_id from tbl_usuarios where usua_identificacion = $vardocumento1")->queryScalar();
                                        Yii::$app->db->createCommand()->insert('tbl_equipos',[
                                            'name' => $varnombreequipo,
                                            'nmumbral_verde' => $varverde,
                                            'nmumbral_amarillo' => $varamarillo,
                                            'usua_id' => $varidusua,
                                        ])->execute();
                                    }
                                }
                            }
  //crear evaluados  
                                      for ($c=0; $c < $numero; $c++) {
                                        $varArray = $datos[$c]; 
                                        $varDatos = explode(";", utf8_encode($varArray));           
                                        
                                            Yii::$app->db->createCommand()->insert('tbl_evaluados',[
                                                'name' => $varDatos[1],
                                                'dsusuario_red' => $varDatos[5],
                                                'identificacion' => $varDatos[0],
                                                'idpcrc' => $varDatos[4],
                                                'usua_id' => $sessiones,
                                                'fechacreacion' => $txtfechacreacion,
                                            ])->execute();

                                        } 

                                        $valicedula = 0;
                                        for ($c=0; $c < $numero; $c++) {
                                            $varArray = $datos[$c]; 
                                            $varDatos = explode(";", utf8_encode($varArray));
                                            $varnombrepcrc = $varDatos[3];
                                            $vardocumentojefe = $varDatos[6];
                                            if($varDatos[6] != $valicedula) {
                                                $valicedula = $varDatos[6];          
                                                $varidlider = Yii::$app->db->createCommand("SELECT usua_id FROM tbl_usuarios  WHERE usua_identificacion = $vardocumentojefe")->queryScalar();
                                                if($varidlider) {
                                                    $varidequipo = Yii::$app->db->createCommand("SELECT id FROM tbl_equipos  WHERE usua_id = $varidlider")->queryScalar();
                                                    $varidarbol = Yii::$app->db->createCommand("SELECT id FROM tbl_arbols  WHERE name = '$varnombrepcrc'")->queryScalar();
                                                    if($varidarbol){
                                                        if ($varidequipo) {
                                                            $varlistaidarbol = Yii::$app->db->createCommand("SELECT id FROM tbl_arbols WHERE arbol_id = $varidarbol")->queryAll();   
                                                            
                                                            foreach ($varlistaidarbol as $key => $value) {
                                                                $varidarbols = $value['id'];                                       
                                                                $varcanti = Yii::$app->db->createCommand("SELECT COUNT(id) FROM tbl_arbols_equipos  WHERE arbol_id = $varidarbols AND equipo_id = $varidequipo")->queryScalar();  
                                                                if($varcanti = 0) {
                                                                    Yii::$app->db->createCommand()->insert('tbl_arbols_equipos',[
                                                                        'arbol_id' => $varidarbols,
                                                                        'equipo_id' => $varidequipo,                            
                                                                    ])->execute();
                                                                }
                            
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
            // fin importe           
                                }
                                fclose($gestor);
                        
                                return $this->redirect('index');
                                    }
                                }
                            }
                        
                            return $this->renderAjax('importarmensaje',[
                            'model' => $model,
                            ]);
                        
                        }
        
              
        }        