<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;

$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-3">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;

$txtcontar = count($txtidbloque);


?>

<div class="capaCero" style="display: inline">
    <a id="dlink" style="display:none;"></a>
    <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar a Excel</button>
</div>
<div class="capaUno" style="display: none">
    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
    <caption>Alinear</caption>
        <thead>

            <tr>
                <td colspan="16" style="font-size:110%;background-color: #28559B; color: #fff;text-align: center;"><?= Yii::t('app', 'KONECTA- QA MANAGEMENT') ?></td>
            </tr>
            <tr>
                <td colspan="16" style="font-size:160%;text-align: center;"><?= Yii::t('app', 'Reporte Alinear + - VOC -') ?></td>
            </tr>
            
        </thead>
    <tbody>
            <?php
                $txtdataID = null;
                $txtUsuanombre = null;
                $txtNombre = null;
                $txtDsname = null;
                $txtDimensions = null;
                $varUsuaid = null;
                $varRolid = null;
                $varequipoid= null;
                $txtValorador = null;
                $txtArbol_id = null;
                $txtFechacreacion = null;
                $txtTecnico = null;
                $txttitulo = '-------';

                $txtcanti=0;
                $txtSesion2=null;
                $varDimension = '"Alinear + VOC"'; 
                $txtcanti=0;
                if ($txtcontar > 1) {
                   $txtValorador = $txtidbloque[1];
                   $txtArbol_id = $txtidbloque[2];
                   $txtFechacreacion = $txtidbloque[3];
                   $txtTecnico = $txtidbloque[4];
                   $txtFecha = explode(" ", $txtidbloque[3]);
                   $txtcanti = count($txtFecha);
                     if ($txtcanti > 1) {
                        $txtfechaini = $txtFecha[0];
                        $txtfechafin = $txtFecha[2]; 
                     }                  
                 }
                 $sessiones = Yii::$app->user->identity->id;   
                
                 $varAnulado = 0;
                 $varMed = 2;
                 $varBog = 98;
                 $varK = 1;
                $sessiones = Yii::$app->user->identity->id;   
            
                 $varAnulado = 0;
                 $varMed = 2;
                 $varBog = 98;
                 $varK = 1;
                 $rol =  new Query;
             $rol      ->select(['tbl_roles.role_id'])
                       ->from('tbl_roles')
                           ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                 'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                           ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                           'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                           ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
            $command = $rol->createCommand();
            $roles = $command->queryScalar();
  
              if ($roles == "270"){

                        $querys =  new Query;
                        $querys     ->select(['tbl_controlvoc_bloque1.idbloque1', 'tbl_controlvoc_bloque1.fechacreacion','tbl_usuarios.usua_nombre', 'rel_usuarios_roles.rel_role_id', 'tbl_evaluados.name', 'tbl_arbols.dsname_full', 'tbl_arbols.arbol_id', 'tbl_arbols.formulario_id', 'tbl_evaluados.identificacion', 'tbl_controlvoc_bloque1.dimensions', 'tbl_equipos.usua_id', 'tbl_equipos.id'])->distinct()
                                    ->from('tbl_controlvoc_bloque1')
                                    ->join('LEFT JOIN', 'tbl_arbols',
                                            'tbl_controlvoc_bloque1.arbol_id = tbl_arbols.id')
                                    ->join('LEFT JOIN', 'tbl_usuarios',
                                            'tbl_controlvoc_bloque1.valorador_id = tbl_usuarios.usua_id')
                                    ->join('LEFT JOIN', 'rel_usuarios_roles',
                                            'tbl_usuarios.usua_id = rel_usuarios_roles.rel_usua_id')
                                    ->join('LEFT JOIN', 'tbl_evaluados',
                                            'tbl_controlvoc_bloque1.tecnico_id = tbl_evaluados.id')
                                    ->join('LEFT JOIN', 'tbl_equipos_evaluados',
                                            'tbl_evaluados.id = tbl_equipos_evaluados.evaluado_id')
                                    ->join('LEFT JOIN', 'tbl_equipos',
                                            'tbl_equipos.id = tbl_equipos_evaluados.evaluado_id' );
                           $querys  -> where('tbl_arbols.activo = '.$varAnulado.'');
                           $querys  -> andwhere('tbl_arbols.arbol_id != '.$varK.'');
                           $querys  -> andWhere('tbl_controlvoc_bloque1.dimensions = '.$varDimension.'');

                    if ($txtValorador != null) { 
                       $querys  -> andwhere('tbl_usuarios.usua_id = '.$txtValorador.'');
                                    }
                    if ($txtArbol_id != null)  { 
                       $querys      -> andwhere('tbl_arbols.id = '.$txtArbol_id.'');
                                        }           
                    if ($txtTecnico != null) { 
                           $querys    -> andwhere('tbl_evaluados.id = '.$txtTecnico.'');
                                       }
                                if ($txtcanti > 1) {
                           $querys    -> andwhere("tbl_controlvoc_bloque1.fechacreacion between '$txtfechaini' and '$txtfechafin'");
                                        }
                         }
                         else{
                                $querys =  new Query;
                            $querys     ->select(['tbl_controlvoc_bloque1.idbloque1', 'tbl_controlvoc_bloque1.fechacreacion','tbl_usuarios.usua_nombre', 'rel_usuarios_roles.rel_role_id', 'tbl_evaluados.name', 'tbl_arbols.dsname_full', 'tbl_arbols.arbol_id', 'tbl_arbols.formulario_id', 'tbl_evaluados.identificacion', 'tbl_controlvoc_bloque1.dimensions', 'tbl_equipos.usua_id', 'tbl_equipos.id'])->distinct()
                                        ->from('tbl_controlvoc_bloque1')
                                        ->join('LEFT JOIN', 'tbl_arbols',
                                                'tbl_controlvoc_bloque1.arbol_id = tbl_arbols.id')
                                        ->join('LEFT JOIN', 'tbl_usuarios',
                                                'tbl_controlvoc_bloque1.valorador_id = tbl_usuarios.usua_id')
                                        ->join('LEFT JOIN', 'rel_usuarios_roles',
                                                'tbl_usuarios.usua_id = rel_usuarios_roles.rel_usua_id')
                                        ->join('LEFT JOIN', 'tbl_evaluados',
                                                'tbl_controlvoc_bloque1.tecnico_id = tbl_evaluados.id')
                                        ->join('LEFT JOIN', 'tbl_equipos_evaluados',
                                                'tbl_evaluados.id = tbl_equipos_evaluados.evaluado_id')
                                        ->join('LEFT JOIN', 'tbl_equipos',
                                                'tbl_equipos.id = tbl_equipos_evaluados.evaluado_id' );
                               $querys  -> where('tbl_usuarios.usua_id = '.$sessiones.'');
                               $querys  -> andwhere('tbl_arbols.activo = '.$varAnulado.'');
                               $querys  -> andwhere('tbl_arbols.arbol_id != '.$varK.'');
                               $querys  -> andWhere('tbl_controlvoc_bloque1.dimensions = '.$varDimension.'');

                    if ($txtValorador != null) { 
                       $querys  -> andwhere('tbl_usuarios.usua_id = '.$txtValorador.'');
                                    }
                    if ($txtArbol_id != null)  { 
                       $querys      -> andwhere('tbl_arbols.id = '.$txtArbol_id.'');
                                        }           
                    if ($txtTecnico != null) { 
                           $querys    -> andwhere('tbl_evaluados.id = '.$txtTecnico.'');
                                       }
                                if ($txtcanti > 1) {
                           $querys    -> andwhere("tbl_controlvoc_bloque1.fechacreacion between '$txtfechaini' and '$txtfechafin'");
                                        }
                            } 
                                  
                    $command = $querys->createCommand();
                    $query = $command->queryAll();

                    foreach ($query as $key => $value) {
                            $txtdataID = $value['idbloque1'];
                            $txtFechacreacion = $value['fechacreacion'];
                            $txtUsuanombre = $value['usua_nombre'];
                            $varRolid = $value['rel_role_id'];
                            $txtProgramapcrc = $value['dsname_full'];
                            $varProgramapcrc = $value['arbol_id'];
                            $varFormularioid = $value['formulario_id'];
                            $txtNombre = $value['name'];
                            $txtDsname = $value['identificacion'];
                            $txtDimensions = $value['dimensions'];
                            $varUsuaid = $value['usua_id'];
                            $varequipoid = $value['id'];

                    
                    $txtIdVoc = $txtdataID;                    
                    $txtProgramapadre = Yii::$app->db->createCommand("select name from tbl_arbols where arbol_id = $varProgramapcrc")->queryScalar();
                    $txtFormularioname = Yii::$app->db->createCommand("select name from tbl_formularios where id = $varFormularioid")->queryScalar();
                    
                    if ($varUsuaid != null) { 
                       $txtRespon = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varUsuaid")->queryScalar();
                      }
                      else
                      {
                        $txtRespon = '';
                      }
                    if ($varRolid != null) {
                       $txtRolname = Yii::$app->db->createCommand("select role_nombre from tbl_roles where role_id = $varRolid")->queryScalar();
                      }
                      else
                      {
                        $txtRolname = '';
                      }
                    if ($varequipoid!= null) {
                       $txtNombreequipo = Yii::$app->db->createCommand("select name from tbl_equipos where id = $varequipoid")->queryScalar();

                      }
                      else
                      {
                        $txtNombreequipo = '';
                      }
                    
                    $varValorador = Yii::$app->db->createCommand("select valorador_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                     
                    $varArbol = Yii::$app->db->createCommand("select arbol_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtArbol = Yii::$app->db->createCommand("select name from tbl_arbols where id = $varArbol and activo = 0")->queryScalar(); 
                    $varTecnico = Yii::$app->db->createCommand("select tecnico_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtNombreTecnico = Yii::$app->db->createCommand("select name from tbl_evaluados where id = $varTecnico")->queryScalar(); 
                    $txtDimensiones = Yii::$app->db->createCommand("select dimensions from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtFecha = Yii::$app->db->createCommand("select fechahora from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtAgente = Yii::$app->db->createCommand("select usuagente from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtDuracion = Yii::$app->db->createCommand("select duracion from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtExtension = Yii::$app->db->createCommand("select extencion from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                    $txtSpeech = Yii::$app->db->createCommand("select numidextsp from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();                    
                ?>
            <tr>           
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Fecha y Hora de la valoracion') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Fecha y Hora de la llamada') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Dimension') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Programa/Pcrc Padre') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Programa/Pcrc') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Formulario') ?></th>            
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Cedula Valorado') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Valorado') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Responsable') ?></th>            
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Valorador') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Rol') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Id Externo Speech') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Usuario de Agente') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Equipo') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Duracion') ?></th>
                <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Extension') ?></th>
            </tr>
                <tr>
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtFechacreacion; ?></td>
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtFecha; ?></td>                    
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtDimensions; ?></td> 
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtProgramapadre; ?></td> 
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtProgramapcrc; ?></td>
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtFormularioname; ?></td>                  
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtDsname; ?></td>
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtNombre; ?></td>
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtRespon; ?></td>
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtUsuanombre; ?></td>
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtRolname; ?></td>                    
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtSpeech; ?></td>
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtAgente; ?></td>
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtNombreequipo; ?></td>
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtDuracion; ?></td>
                    <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtExtension; ?></td>
                </tr>
                <tr>
                    <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Nombre Sesion') ?></th>
                    <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Nombre Categoria') ?></th>
                    <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Atributos') ?></th>
                    <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Medicion') ?></th>
                    <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Conclusiones') ?></th>
                 </tr>
                <?php   

                   $txtPlanAccion = Yii::$app->db->createCommand("select * from tbl_control_alinear_bloque3 where idbloque1 = '$txtIdVoc'")->queryAll();

                          foreach ($txtPlanAccion as $key => $value) {
                              $txtConcepto_mejora = $value['concepto_mejora'];
                              $txtAnalisis_causa = $value['analisis_causa'];
                              $txtAccion_seguir = $value['accion_seguir'];
                              $txtTipo_accion = $value['tipo_accion'];
                              $txtResponsable = $value['responsable'];
                              $txtFecha_plan = $value['fecha_plan'];
                              $txtFecha_implementa = $value['fecha_implementa'];
                              $txtEstado = $value['estado'];           
                              $txtObservacion = $value['observaciones'];
                              $txtSesion2 = $value['sesion_id'];
                          }              

                       $varcodigos = Yii::$app->db->createCommand(" select tbl_control_alinear_participa.codigo_partcipa
                            FROM tbl_control_alinear_participa
                            WHERE tbl_control_alinear_participa.idbloque1 = '$txtIdVoc'")->queryScalar();
                        $arrayCodigos = explode(",",$varcodigos);
                        $txtNombrePartic = "";
                        for ($i=0; $i <count($arrayCodigos); $i++) { 
                                 $valor = $arrayCodigos[$i];
                             
                        $txtNombrePartic2 = Yii::$app->db->createCommand("select tbl_participantes.participan_nombre from tbl_participantes WHERE tbl_participantes.participan_id = '$valor'")->queryScalar();
                        $txtNombrePartic = $txtNombrePartic . ', ' . $txtNombrePartic2;
                        }
                        $txtNombrePartic = substr($txtNombrePartic, 1);
                   $txtQuery2 =  new Query;


                   if($txtSesion2 == 3){
                        $txtQuery2  ->select(['tbl_arbols.name', 'tbl_sesion_alinear.sesion_nombre', 'tbl_categorias_alinear.categoria_nombre', 'tbl_atributos_alinear.atributo_nombre', 'tbl_atributos_alinear.id_atrib_alin'])
                                ->from('tbl_categorias_alinear')
                                ->join('INNER JOIN', 'tbl_atributos_alinear',
                                     'tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali')
                                ->join('INNER JOIN', 'tbl_arbols',
                                     'tbl_categorias_alinear.arbol_id = tbl_arbols.id')
                                ->join('INNER JOIN', 'tbl_sesion_alinear',
                                     'tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id')                    
                                ->where(['tbl_arbols.id' => $varArbol]);
                   }else{
                         $txtQuery2  ->select(['tbl_arbols.name', 'tbl_sesion_alinear.sesion_nombre', 'tbl_categorias_alinear.categoria_nombre', 'tbl_atributos_alinear.atributo_nombre', 'tbl_atributos_alinear.id_atrib_alin'])
                                ->from('tbl_categorias_alinear')
                                ->join('INNER JOIN', 'tbl_atributos_alinear',
                                     'tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali')
                                ->join('INNER JOIN', 'tbl_arbols',
                                     'tbl_categorias_alinear.arbol_id = tbl_arbols.id')
                                ->join('INNER JOIN', 'tbl_sesion_alinear',
                                     'tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id')
                                ->where(['tbl_arbols.id' => $varArbol])
                                ->andwhere(['tbl_sesion_alinear.sesion_id' => $txtSesion2]);
                   }
                    
                $command = $txtQuery2->createCommand();
                $dataProvider = $command->queryAll();


                                
                  
                    $varfila = 0;
        
                    foreach ($dataProvider as $key => $value) {
                        $varNomList = $value['name'];         
                        $varSesionnomList = $value['sesion_nombre'];
                        $varCategonomList = $value['categoria_nombre'];
                        $varAtribunomList = $value['atributo_nombre'];
                        $varIdAtributoList = $value['id_atrib_alin'];
                        $varfila = $varfila + 1;   

                         $varMedicionnomList = null;
                         $varAcuerdoList = null;
                    $txtQuery3 =  new Query;
                    $txtQuery3  ->select(['tbl_medir_alinear.medicion', 'tbl_medir_alinear.acuerdo'])
                                ->from('tbl_categorias_alinear')
                                ->join('INNER JOIN', 'tbl_atributos_alinear',
                                     'tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali')
                                ->join('INNER JOIN', 'tbl_arbols',
                                     'tbl_categorias_alinear.arbol_id = tbl_arbols.id')
                                ->join('INNER JOIN', 'tbl_sesion_alinear',
                                     'tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id')
                                ->join('INNER JOIN', 'tbl_medir_alinear',
                                     'tbl_medir_alinear.id_atrib_alin = tbl_atributos_alinear.id_atrib_alin')
                                ->where(['tbl_arbols.id' => $varArbol])
                                ->andwhere(['tbl_medir_alinear.id_idvalorado' => $varTecnico])
                                ->andwhere(['tbl_medir_alinear.id_atrib_alin' => $varIdAtributoList])
                                ->andwhere(['tbl_medir_alinear.idbloque1' => $txtIdVoc]);
                    $command1 = $txtQuery3->createCommand();
                    $dataProvider2 = $command1->queryAll();

                    foreach ($dataProvider2 as $key => $value1) {
                       $varMedicionnomList = $value1['medicion'];
                       $varAcuerdoList = $value1['acuerdo'];
                      }                  
                  

            ?>               
                 <tr>   
                    <td class="text-center"><?php echo $varSesionnomList; ?></td>
                    <td class="text-center"><?php echo $varCategonomList; ?></td>
                    <td class="text-center"><?php echo $varAtribunomList; ?></td>
                    <td class="text-center"><?php echo $varMedicionnomList; ?></td>
                    <td class="text-center"><?php echo $varAcuerdoList; ?></td>
                    <?php
                       }            
                     ?>

                     <?php
                       $txtContarSesion1 = null;
                       $txtContarSesion2 = null;
                       if($txtSesion2 == 3){
                            $txtContarSi1 = Yii::$app->db->createCommand("select COUNT(tbl_atributos_alinear.atributo_nombre) AS cuentasi
                                FROM tbl_categorias_alinear
                                INNER JOIN tbl_atributos_alinear ON tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali
                                INNER JOIN tbl_arbols ON tbl_categorias_alinear.arbol_id = tbl_arbols.id
                                INNER JOIN tbl_sesion_alinear on tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id
                                INNER JOIN tbl_medir_alinear ON tbl_medir_alinear.id_atrib_alin = tbl_atributos_alinear.id_atrib_alin
                                where tbl_arbols.id = '$varArbol' and tbl_medir_alinear.id_idvalorado = '$varTecnico' and tbl_categorias_alinear.sesion_id in(1, 2) AND tbl_medir_alinear.medicion = 'Si' AND tbl_medir_alinear.idbloque1 = '$txtIdVoc'")->queryScalar();

                            $txtContarSesion1 = Yii::$app->db->createCommand("select COUNT(tbl_atributos_alinear.atributo_nombre) AS cuentasi
                            FROM tbl_categorias_alinear 
                            INNER JOIN tbl_arbols ON tbl_categorias_alinear.arbol_id = tbl_arbols.id
                            INNER JOIN tbl_atributos_alinear ON tbl_atributos_alinear.id_categ_ali = tbl_categorias_alinear.id_categ_ali
                            INNER JOIN tbl_sesion_alinear ON tbl_sesion_alinear.sesion_id = tbl_categorias_alinear.sesion_id
                            WHERE tbl_categorias_alinear.arbol_id = '$varArbol' AND tbl_categorias_alinear.sesion_id in(1, 2) ")->queryScalar();
                     }
                     if($txtSesion2 != 3){
                            $txtContarSi2 = Yii::$app->db->createCommand("select COUNT(tbl_atributos_alinear.atributo_nombre) AS cuentasi
                                FROM tbl_categorias_alinear
                                INNER JOIN tbl_atributos_alinear ON tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali
                                INNER JOIN tbl_arbols ON tbl_categorias_alinear.arbol_id = tbl_arbols.id
                                INNER JOIN tbl_sesion_alinear on tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id
                                INNER JOIN tbl_medir_alinear ON tbl_medir_alinear.id_atrib_alin = tbl_atributos_alinear.id_atrib_alin
                                where tbl_arbols.id = '$varArbol' and tbl_medir_alinear.id_idvalorado = '$varTecnico' and tbl_categorias_alinear.sesion_id = '$txtSesion2' AND tbl_medir_alinear.medicion = 'Si' AND tbl_medir_alinear.idbloque1 = '$txtIdVoc'")->queryScalar();

                             $txtContarSesion2 = Yii::$app->db->createCommand("select COUNT(tbl_atributos_alinear.atributo_nombre) AS cuentasi
                                FROM tbl_categorias_alinear 
                                INNER JOIN tbl_arbols ON tbl_categorias_alinear.arbol_id = tbl_arbols.id
                                INNER JOIN tbl_atributos_alinear ON tbl_atributos_alinear.id_categ_ali = tbl_categorias_alinear.id_categ_ali
                                INNER JOIN tbl_sesion_alinear ON tbl_sesion_alinear.sesion_id = tbl_categorias_alinear.sesion_id
                                WHERE tbl_categorias_alinear.arbol_id = '$varArbol' AND tbl_categorias_alinear.sesion_id =  '$txtSesion2' ")->queryScalar();
                      }

                            $indicadorPrecic1 = "";
                            $indicadorPrecic2 = "";
                       
                            if ($txtContarSesion1) {
                                  $indicadorPrecic1 = number_format($txtContarSi1 / $txtContarSesion1,2) * 100;
                            }

                            if ($txtContarSesion2) {
                                  $indicadorPrecic1 = number_format($txtContarSi2 / $txtContarSesion2,2) * 100;
                            }
                        ?>
                    </tr>    
                        <tr>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"><strong>Indicador de Precision</strong></td>
                            <td class="text-center"><?php echo $indicadorPrecic1; ?>%</td>
                            <td class="text-center"></td>
                        </tr>
                                     
                       <tr>
                          <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Participantes') ?></th>
                          <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Concepto de mejora') ?></th>
                          <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Analisis de Causa') ?></th>
                          <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Accion a Seguir') ?></th>
                          <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Tipo de accion') ?></th>
                          <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Responsable') ?></th>
                          <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Fecha Plan') ?></th>
                          <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Fecha de Implementacion') ?></th>
                          <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Estado') ?></th>               
                          <th scope="col" class="text-center" style="background-color: #28559B; color: #fff"><?= Yii::t('app', 'Observaciones') ?></th>
                      </tr>
                      <tr>
                          <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtNombrePartic; ?></td>
                          <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtConcepto_mejora; ?></td>
                          <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtAnalisis_causa; ?></td>
                          <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtAccion_seguir; ?></td>
                          <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtTipo_accion; ?></td>
                          <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtResponsable; ?></td>
                          <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtFecha_plan; ?></td>
                          <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtFecha_implementa; ?></td>
                          <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtEstado; ?></td>                    
                          <td class="text-center" style="background-color: #4298B5; color: #fff"><?php echo $txtObservacion; ?></td>
                       </tr>
              <tr>
                <td colspan="16" style="font-size:110%;background-color: #C6C6C6;text-align: center;"></td>
              </tr>
                    
                <?php } ?>
            
    </tbody>
    </table>
</div>
<script type="text/javascript" charset="UTF-8">
var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            console.log(uri + base64(format(template, ctx)));
            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = "DashBoard Formulario Voc";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Archivo Voc', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>

