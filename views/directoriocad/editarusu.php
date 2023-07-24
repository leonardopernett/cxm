
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

$this->title = 'Directorio Cad';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Directorio Cad';

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

  $varPermisos = (new \yii\db\Query())
                ->select(['*'])
                ->from(['tbl_hojavida_permisosacciones'])
                ->where(['=','usuario_registro',$sessiones]) 
                ->andwhere(['=','anulado',0])
                ->all();

  $varEliminar = null;
  $varResumen = null;
  $varInformacion = null;
  $varCargar = null;
  $varEditar = null;
  foreach ($varPermisos as $key => $value) {
    $varEliminar = $value['hveliminar'];
    $varResumen = $value['hvverresumen'];
    $varInformacion = $value['hvdatapersonal'];
    $varCargar = $value['hvcasrgamasiva'];
    $varEditar = $value['hveditar'];
  }
  
  $varArrayCiudadCliente = array();
  $varArrayConteoCliente = array();
  $varSectortwo = Yii::$app->db->createCommand("select * from tbl_sector_cad")->queryAll();

  $listData2 = ArrayHelper::map($varSectortwo, 'id_sectorcad', 'nombre');

  $varProveedorestwo = Yii::$app->db->createCommand("select * from tbl_proveedores_cad")->queryAll();

  $listData3 = ArrayHelper::map($varProveedorestwo, 'id_proveedorescad', 'name');

  $varTipotwo = Yii::$app->db->createCommand("select * from tbl_tipo_cad")->queryAll();

  $listData4 = ArrayHelper::map($varTipotwo, 'id_tipocad', 'nombre');

  $varTipoCanaltwo = Yii::$app->db->createCommand("select * from tbl_tipocanal_cad")->queryAll();

  $listData5 = ArrayHelper::map($varTipoCanaltwo, 'id_tipocanalcad', 'nombre');

  $varEtapatwo = Yii::$app->db->createCommand("select * from tbl_etapa_cad")->queryAll();

  $listData6 = ArrayHelper::map($varEtapatwo, 'id_etapacad', 'nombre');

  $varCiudadtwo = Yii::$app->db->createCommand("select * from tbl_ciudad_cad")->queryAll();

  $listData7 = ArrayHelper::map($varCiudadtwo, 'id_ciudad_cad', 'nombre');

  $varVicetwo = Yii::$app->db->createCommand("select * from tbl_vicepresidente_cad")->queryAll();

  $listData8 = ArrayHelper::map($varVicetwo, 'id_vicepresidentecad', 'nombre');

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
    background-image: url('../../images/directorio_cad.png');
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


<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<br>

<div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Editar Cuentas"; ?> </label>
      </div>
    </div>
  </div>
<br>
<div class="row">
    <div class="col-md-12">

        <div class="row">
            <div class="col-md-12">

                <div class="row">
                            
                    <div class="col-md-12">
                        <div class="card1 mb">

                            <div class="row">
                                    
                                <div class="col-md-6">

                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Cliente:') ?></label> 
                                    <?=  $form->field($model, 'cliente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                                            [
                                                              'id' => 'idinfocliente',
                                                              'prompt'=>'Seleccionar Servicio...',
                                                              'onchange' => '
                                                                  $.get(
                                                                      "' . Url::toRoute('directoriocad/listardirectores') . '", 
                                                                      {id: $(this).val()}, 
                                                                      function(res){
                                                                          $("#requester2").html(res);
                                                                      }
                                                                  );
                                                               
                                                                  $.get(
                                                                      "' . Url::toRoute('directoriocad/listargerentes') . '", 
                                                                      {id: $(this).val()}, 
                                                                      function(res){
                                                                          $("#requester3").html(res);
                                                                      }
                                                                   );
                                                              ',
                                                            ]
                                              )->label(''); 
                                            ?>                                
                                        
            
                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Vicepresidente:') ?></label> 
                                    <?= $form->field($model,'vicepresidente',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData8,['prompt'=>'Seleccionar...', 'id' => 'vicepresidente'])?>
                                
                                                                 
                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Director del Programa :') ?></label> 
                                    <?= $form->field($model,'directorprog', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                [],
                                                [
                                                    'prompt' => 'Seleccionar Director...',
                                                    'id' => 'requester2',
                                                    'onclick' => '
                                                        
                                                        $.get(
                                                            "' . Url::toRoute('directoriocad/listarpcrcindex') . '", 
                                                            {id: $(this).val()}, 
                                                            function(res){
                                                                  $("#requester").html(res);
                                                            }
                                                        );
                                                    ',
                                                ]
                                                  )->label('');
                                              ?> 

                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Gerente:') ?></label> 
                                    <?= $form->field($model,'gerente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                [],
                                                [
                                                  'prompt' => 'Seleccionar Gerente...',
                                                  'id' => 'requester3',
                                                  'onclick' => '
                                                      
                                                      $.get(
                                                          "' . Url::toRoute('directoriocad/listarpcrcindex') . '", 
                                                          {id: $(this).val()}, 
                                                          function(res){
                                                                $("#requester").html(res);
                                                          }
                                                      );
                                                  ',   
                                                ]
                                                  )->label('');
                                              ?>
                                              
                                    

                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Ingrese la Sociedad:') ?></label> 
                                    <?= $form->field($model, 'sociedad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'sociedad', 'placeholder'=>'Seleccionar Sociedad...'])->label('') ?>
                                
                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Ciudad:') ?></label> 
                                    <?= $form->field($model,'ciudad',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData7,['prompt'=>'Seleccionar...', 'id' => 'ciudad'])?>
                                
                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Sector:') ?></label> 
                                    <?= $form->field($model,'sector',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData2,['prompt'=>'Seleccionar...', 'id' => 'sector'])?>
                                
                                   

                                </div>

                                <div class="col-md-6">
            
                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Tipo:') ?></label> 
                                    <?= $form->field($model,'tipo',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData4,['prompt'=>'Seleccionar...', 'id' => 'tipo'])?>
                                
                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Seleccionar Tipo de Canal:') ?></label> 
                                    <?= $form->field($model,'tipo_canal',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData5,['prompt'=>'Seleccionar...', 'id' => 'tipo_canal'])?>
                                
                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Otro Canal:') ?></label> 
                                    <?= $form->field($model, 'otro_canal', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'otro_canal', 'placeholder'=>'Otro Canal...'])->label('') ?>
                                
                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Proveedores:') ?></label> 
                                    <?= $form->field($model,'proveedores',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData3,['prompt'=>'Seleccionar...',"onchange" => 'varValida();', 'id' => 'proveedores'])?>
                                
                                    <div id="IdBloque" style="display:none;">
                                        <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Nombre de la Plataforma:') ?></label> 
                                        <?= $form->field($model, 'nom_plataforma', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 500, 'id' => 'nom_plataforma', 'placeholder'=>'Nombre de la Plataforma...'])->label('') ?>
                                    </div>
                                    <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #C31CB4;"></em> <?= Yii::t('app', 'Etapa:') ?></label> 
                                    <?= $form->field($model,'etapa',['labelOptions' => ['class'=>'col-md-12'],'template' => $template])->dropDownList($listData6,['prompt'=>'Seleccionar...', 'id' => 'etapa','multiple' => true])?>

                                    
                                </div>  
                            </div>         
                        </div>
                    </div>
                </div>
            </div>      
        </div>
    </div>
</div>


<br><hr>

<div class="row">
    <div class="col-md-6">
        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #C31CB4;"></em> Cancelar y regresar: </label> 
            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
            ?>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #C31CB4;"></em> Guardar Datos: </label>
            <?= Html::submitButton(Yii::t('app', 'Guardar y Enviar'),
                                                          ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                              'data-toggle' => 'tooltip',
                                                              'onclick' => 'varVerificar();',
                                                              'title' => 'Registro General']) 
                                                            //  die(json_encode($model));
            ?>
        </div>
    </div>
</div>


<script>

    function varValida(){
    var varidSeleccionar = document.getElementById("proveedores").value;
    var varBloque =  document.getElementById("IdBloque");

    if (varidSeleccionar == "2") {
      varBloque.style.display='inline';
    }else{
      varBloque.style.display='none';
    }

   
  }
</script>