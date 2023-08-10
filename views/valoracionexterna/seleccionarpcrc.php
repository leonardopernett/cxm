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
use app\models\Dashboardcategorias;
use app\models\Dashboardservicios;


$this->title = 'Gestor Valoraciones Externas';
$this->params['breadcrumbs'][] = $this->title;

  $template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol    ->select(['tbl_roles.role_id'])
          ->from('tbl_roles')
          ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                	'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
          ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
          ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
  $command = $rol->createCommand();
  $roles = $command->queryScalar();

?>
<style>
  .card1 {
    height: auto;
    width: auto;
   	margin-top: auto;
    margin-bottom: auto;
    background: #FFFFFF;
    position: relative;
    display: flex;
    justify-content: center;
   	flex-direction: column;
    padding: 10px;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    border-radius: 5px;    
   	font-family: "Nunito",sans-serif;
    font-size: 150%;    
    text-align: left;    
  }

  .card2 {
    height: 355px;
    width: auto;
    margin-top: auto;
    margin-bottom: auto;
    background: #FFFFFF;
    position: relative;
    display: flex;
    justify-content: center;
    flex-direction: column;
    padding: 10px;
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
    -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
    -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
    border-radius: 5px;    
    font-family: "Nunito",sans-serif;
    font-size: 150%;    
    text-align: left;    
  }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >


<!-- Capa Principal -->
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<div id="capaIdPrincipal" class="capaPrincipal" style="display: inline;"> 

    <div class="row">

        <div class="col-md-12">
            <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Acciones') ?></label>
            <?= $form->field($model,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Arboles::find()->where(['=','arbol_id',$id_dp_clientes])->orderBy(['dsname_full'=> SORT_ASC])->all(), 'id', 'dsname_full'),
                                                  [
                                                    'id' => 'cod_pcrc',
                                                    'prompt'=>'Seleccionar...',
                                                  ]
                                    )->label(''); 
            ?>
            <br>
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Seleccionar Pcrc') ?></label>
                   <br> 
                   <?= Html::submitButton(Yii::t('app', 'Guardar y Enviar'),
                                                          ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                              'data-toggle' => 'tooltip',
                                                              'onclick' => 'varVerificar();',
                                                              'title' => 'Registro General']) 
                  ?>
                
            </div>
            <br>
        
            <div class="card1 mb">
                <table id="tblDatapcrc" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?= Yii::t('app', 'Resultados') ?></caption>
                    <thead>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;" class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', 'Programa') ?></label></th>
                        <th scope="col" style="background-color: #b0cdd6;" class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php
                        foreach ($varData as $key => $value) {
                
                    ?>
                   
                        <tr>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $value['dsname_full']; ?></label></td>
                        <td class="text-center">

                            <?= 
                            Html::a('<em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em>',  ['subircarga','cod_pcrc' => $value['cod_pcrc'],'id_dp_clientes' => $value['id_dp_clientes']],['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Editar Usuario']) 
                            ?>

                        </td>
                        
                        
                        </tr>
                   
                    </tbody>
                    <?php
                        }
                    ?>
                </table>
            </div>
        </div>
        


        
    </div>
</div>

<?php ActiveForm::end(); ?>

<script>
  
  <?php  if(base64_decode(Yii::$app->request->get("varAlerta")) === "1"){ ?>       
    
    swal.fire("Información","Accion ejecutada Correctamente","success"); 
    <?php } ?>
    
</script>