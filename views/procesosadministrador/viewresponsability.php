<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\db\Query;

$this->title = 'Procesos Administrador - Responsabilidades';
$this->params['breadcrumbs'][] = $this->title;

$sesiones =Yii::$app->user->identity->id;   

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';


$rol =  new Query;
$rol    ->select(['tbl_roles.role_id'])
        ->from('tbl_roles')
        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
        ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
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


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
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
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="capapp" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
          <div class="card1 mb" style="background: #6b97b1; ">
            <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Acciones de Responsabilidades"; ?> </label>
          </div>
        </div>
    </div>

    <br>

    <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #C148D0;"></em> Lista de Responsabilidades: </label>
                
                <?php if (count($varListresponsabilidad ) != null) { ?>
                <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                    <thead>
                        <tr>
                            <th scope="col" colspan="2" style="text-align: center; background-color: #d2d0d0;"><label style="font-size: 13px;"><?php echo $varnombrepcrc; ?></label></th>
                        </tr>
                        <tr>    
                            <th scope="col" style="text-align: center; background-color: #d2d0d0;"><label style="font-size: 13px;"><?php echo "Proceso"; ?></label></th>
                            <th scope="col" style="text-align: center; background-color: #d2d0d0;"><label style="font-size: 13px;"><?php echo "Responsabilidad"; ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                    
                        <?php                    
                        foreach ($varListresponsabilidad as $key => $value) {
                            
                        ?>
                        <tr>
                            <td><label style="font-size: 13px;"><?php echo $value['nombre']; ?></label></td>
                            <td><label style="font-size: 13px;"><?php echo $value['tipo']; ?></label></td>
                            </tr>
                        <?php 
                        }
                        ?>
                    
                    </tbody>
                </table>       
                <?php } ?>         
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 15px; color: #C148D0;"></em> Cantidad de Responsabilidades: </label>
                        <label  style="font-size: 70px; text-align: center;"><?php echo $txtConteo; ?></label>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-sticky-note" style="font-size: 15px; color: #C148D0;"></em> Buscar Pcrc/Programa... </label>
                        <?=
                            $form->field($model, 'procesos', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                                        ->widget(Select2::classname(), [
                                            //'data' => array_merge(["" => ""], $data),
                                            'language' => 'es',
                                            'options' => ['id'=>'idvararbol', 'placeholder' => Yii::t('app', 'Select ...')],
                                            'pluginOptions' => [
                                                'allowClear' => false,
                                                'minimumInputLength' => 3,
                                                'ajax' => [
                                                    'url' => \yii\helpers\Url::to(['getarbolesbyroles']),
                                                    'dataType' => 'json',
                                                    'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                                    'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                                ],
                                            //'initSelection' => new JsExpression($initScript)
                                            ]
                            ])->label('');
                        ?>  
                        <?= Html::submitButton(Yii::t('app', 'Buscar'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Buscar Responsabilidad',
                                    'onclick' => 'validar();']) 
                        ?>                      
                    </div>
                </div>
            </div>  
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-info" style="font-size: 15px; color: #C148D0;"></em> Acciones... </label>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <?php  if ($txtConteo == 0 && $varidarbol != null) { ?>
                                    <?=
                                        $form->field($model, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                                                    ->widget(Select2::classname(), [
                                                        //'data' => array_merge(["" => ""], $data),
                                                        'language' => 'es',
                                                        'options' => ['id'=>'idvararboltwo', 'placeholder' => Yii::t('app', 'Select ...')],
                                                        'pluginOptions' => [
                                                            'allowClear' => false,
                                                            'minimumInputLength' => 3,
                                                            'ajax' => [
                                                                'url' => \yii\helpers\Url::to(['getarbolesbyroles']),
                                                                'dataType' => 'json',
                                                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                                            ],
                                                        //'initSelection' => new JsExpression($initScript)
                                                        ]
                                        ])->label('');
                                    ?>
                                    <div class="row" style="text-align: center;">
                                        <div onclick="validarvalor();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="ButtonSearch" >
                                            Clonar
                                        </div> 
                                    </div>
                                <?php }else{ ?>
                                    <label for="Wait">--</label>
                                <?php } ?>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #C148D0;"></em> Cancelar y regresar... </label>
                        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $form->end() ?> 
</div>
<hr>
<script>
    function validar(){
        var varidvararbol = document.getElementById("idvararbol").value;
        console.log(varconteo);

        if (varidvararbol == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Debe de ingresar un Pcrc/Programa","warning");
            return;
        }
    };

    function validarvalor(){
        var varidvararboltwo = document.getElementById("idvararboltwo").value;
        var varidvararbol = "<?php echo $varidarbol; ?>";

        if (varidvararbol == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Debe de ingresar un Pcrc/Programa","warning");
            return;
        }else{
            $.ajax({
				method: "get",
				url: "generarregistro",
				data: {
					txtvaridvararboltwo : varidvararboltwo,
                    txtvaridvararbol : varidvararbol,
				},
				success : function(response){
					numRta =   JSON.parse(response);

					if (numRta == 0) {
						event.preventDefault();
			            swal.fire("¡¡¡ Advertencia !!!","El Pcrc no quedo registrado en el sistema","warning");
			            return;
					}else{
						window.location.href='index';						
					}
				}
			});
        }
    };
</script>