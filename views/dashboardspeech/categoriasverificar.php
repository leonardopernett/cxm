<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Dashboardcategorias;

$this->title = 'DashBoard -- Voice Of Customer --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Visualización por Categorias -- QA & Speech --';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;

    $query = Yii::$app->db->createCommand("select distinct sp.rn, sp.ext, sp.usuared, sc.pcrc, sp.comentarios, sc.cod_pcrc, sc.idcategoria, sc.nombre, sc.tipocategoria, sc.tipoindicador, sc.programacategoria, sc.tipoparametro, sc.orientacionsmart, sc.orientacionform, sc.idcategorias from tbl_speech_parametrizar sp inner join tbl_speech_categorias sc on sp.cod_pcrc = sc.cod_pcrc where sc.cod_pcrc in ('$varcod') and sc.anulado = 0")->queryAll();

    $varClient = Yii::$app->db->createCommand("select distinct id_dp_clientes from tbl_speech_parametrizar where anulado = 0 and cod_pcrc in ('$varcod')")->queryScalar();

?>
&nbsp; 
  <?= Html::a('Regresar',  ['categoriasconfig'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
  ?>
<br>
    <div class="page-header" >
        <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
    </div> 
<br>
<div>
<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <thead>
            <tr>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Programa Cliente') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Regla de Negocio') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Extensión') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Usuario Red') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Otros') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'PCRC') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Centro de Costos') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Id Categoria') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Nombre Categoria') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Tipo Categoria') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Tipo Parametro') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Tipo Indicador') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Presentacion Smart') ?></th>
                <th scope="col" class="text-center"><?= Yii::t('app', 'Presentacion Dashboard') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php                               

                foreach ($query as $key => $value) {                    
            ?>
                <tr>
                    <td class="text-center"><?php echo $value['programacategoria'];?></td>
                    <td class="text-center"><?php echo $value['rn'];?></td>
                    <td class="text-center"><?php echo $value['ext'];?></td>
                    <td class="text-center"><?php echo $value['usuared'];?></td>
                    <td class="text-center"><?php echo $value['comentarios'];?></td>
                    <td class="text-center"><?php echo $value['pcrc'];?></td>
                    <td class="text-center"><?php echo $value['cod_pcrc'];?></td>
                    <td class="text-center"><?php echo $value['idcategoria'];?></td>
                    <td class="text-center"><?php echo $value['nombre'];?></td>
                    <td class="text-center"><?php echo $value['tipocategoria'];?></td>
                    <td class="text-center"><?php echo $value['tipoindicador'];?></td>
                    <?php
                        if($value['tipoparametro'] == '1'){ 
                    ?>
                        <td class="text-center"><?php echo "Estrategico";?></td>
                    <?php
                        }else{  
                            if($value['tipoparametro'] == '2') {
                    ?>
                        <td class="text-center"><?php echo "Desempeño";?></td>
                    <?php
                            }else{
                    ?>
                                <td class="text-center"><?php echo "";?></td>

                    <?php
                            }
                        } 
                    ?>

                    <?php
                        if($value['orientacionsmart'] == '2'){
                    ?>
                        <td class="text-center"><?php echo "Negativo";?></td>
                    <?php
                        }else{
                            if($value['orientacionsmart'] == '1'){                            
                    ?>
                        <td class="text-center"><?php echo "Positivo";?></td>
                    <?php
                            }else{
                    ?>
                        <td class="text-center"><?php echo "";?></td>
                    <?php
                            }
                        }
                    ?>

                    <?php
                        if($value['orientacionform'] == '0'){
                    ?>
                        <td class="text-center"><?php echo "Negativo";?></td>
                    <?php
                        }else{
                            if($value['orientacionform'] == '1'){                            
                    ?>
                        <td class="text-center"><?php echo "Positivo";?></td>
                    <?php
                            }else{
                    ?>
                        <td class="text-center"><?php echo "";?></td>
                    <?php
                            }
                        }
                    ?>
                </tr>
            <?php
                }
            ?>
        </tbody>
    </table> 
</div>