<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$variables  = Yii::$app->user->identity->id;

$this->title = Yii::t('app', 'Historico de Alertas');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>


<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Alertas-Valoración.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
    <div class="col-md-6">
        <div class="card1 mb" style="background: #6b97b1; ">
            <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Información"; ?> </label><!--titulo principal de mi modulo-->
        </div>
    </div>
</div>
<br>

<div class="equipos-evaluados-form">    

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card1"> 
                <div class="col-md-12">

                    <?=
                    $form->field($model, 'fecha', [
                        'labelOptions' => ['class' => 'col-md-12'],
                        'template' => '<div class="col-md-4">{label}</div>'
                        . '<div class="col-md-8"><div class="input-group">'
                        . '<span class="input-group-addon" id="basic-addon1">'
                        . '<i class="glyphicon glyphicon-calendar"></i>'
                        . '</span>{input}</div>{error}{hint}</div>',
                        'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                        'options' => ['class' => 'drp-container form-group']
                    ])->widget(DateRangePicker::classname(), [
                        'useWithAddon' => true,
                        'convertFormat' => true,
                        'presetDropdown' => true,
                        'readonly' => 'readonly',
                        'pluginOptions' => [
                            'timePicker' => false,
                            'format' => 'Y-m-d',
                            'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                            'endDate' => date("Y-m-d"),
                            'opens' => 'right'
                    ]]);
                    ?>
                </div>
                <br>
                <div class="col-md-12">
                    <?=
                    $form->field($model, 'pcrc', ['template' => $template])
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'ajax' => [
                        'url' => \yii\helpers\Url::to(['basesatisfaccion/getarbolesbypcrc']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                        ],
                        'initSelection' => new JsExpression('function (element, callback) {
                            var id=$(element).val();
                            if (id !== "") {
                                $.ajax("' . Url::to(['basesatisfaccion/getarbolesbypcrc']) . '?id=" + id, {
                                    dataType: "json",
                                    type: "post"
                                }).done(function(data) { callback(data.results[0]);});
                            }
                        }')
                        ]
                        ]
                        );
                        ?>
                </div>
                <br>
                <div class="col-md-12">            
                        <?=
                            $form->field($model, 'responsable', ['template' => $template])
                            ->widget(Select2::classname(), [
                                'language' => 'es',
                                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 4,
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                    ],
                                    'initSelection' => new JsExpression('function (element, callback) {
                                                var id=$(element).val();
                                                if (id !== "") {
                                                    $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
                                                        dataType: "json",
                                                        type: "post"
                                                    }).done(function(data) { callback(data.results[0]);});
                                                }
                                            }')
                                ]
                                    ] 
                        );
                        ?> 
                </div>   
                <br>
                    <div class="col-md-12">
                        <?=
                        Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
                        ?>
                        <?php //Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default'])      ?>
                    </div>        
                
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                    <div class="card1 mb">
                        <label class="text-center" style="font-size: 20px;"><em class="fas fa-hashtag" style="font-size: 25px; color: #1993a5;"></em> Cantidad de Alertas: </label>
                        
                        <label style="font-size: 30px;" class="text-center" ><?php
                        $var = (new \yii\db\Query())
                            ->select(['count(*)'])
                            ->from(['tbl_alertascx'])
                            ->Scalar();
                            echo $var
                        ?></label>
                        
                    </div>

            </div>
    
            <hr>
            <div class="row">
                    <div class="card1 mb">
                        <label style="font-size: 20px;"><em class="fas fa-download" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Descargar Global') ?></label> 
                        <a id="dlink" style="display:none;"></a>
                        <button  class="btn btn-primary"  id="btn" rel="stylesheet" type="text/css" title="Descagar Global"><?= Yii::t('app', 'Descargar Global') ?></button>
                    
                    </div>
                        
                    </div>

            </div>
        </div>
    </div>

    <br><hr><br>

    <?php ActiveForm::end(); ?>  


    <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Resumen Proceso"; ?> </label><!--titulo principal de mi modulo-->
            </div>
        </div>
    </div>
    <br>
    <div class="row">
    
        
        <div class="col-md-6">
            <!-- inicio del div de la tarjeta de tamaño 8 -->
        
            <div class="card1 mb"><!-- div de la carta principal donde van a ir la informacion ---( me pone la tarjeta)-------------------> 
    
                <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" ><!--Titulo de la tabla no se muestra-->
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Resumen Proceso') ?></label></caption><!--Titulo de la tabla si se muestra-->
                    <thead><!--Emcabezados de la tabla -->
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Programa') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Cantidad de alertas') ?></label></th>
                    </thead>
                
                    
                        <tbody>
                            <?php foreach ($resumenFeedback as $value) : ?>
                                <tr>
                                    <td><?= $value["Programa"]; ?></td>
                                    <td><?= $value["Cliente"]; ?></td>
                                    <td><?= $value["Count"]; ?></td>
                                </tr>
                            <?php endforeach; ?>
                    </tbody><!--fin Tbody de la tabla -->
                </table><!--fin  de la tabla -->
    
            </div><!--fin de la tarjeta dond esta la tabla -->
        </div>

        <div class="col-md-6"><!-- inicio del div de la tarjeta de tamaño 8 -->
        <!-- div de la carta principal donde van a ir la informacion ---( me pone la tarjeta)-------------------> 
            <div class="card123 mb">
                <table style="width:100%">
                    <th scope="col" class="text-center" style="width: 100px;">
                            <label style="font-size: 20px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #1993a5;"></em> <?= Yii::t('app', 'Grafica Tipos de Alertas') ?></label><br>
                            <div id="containeralerta" class="highcharts-container" style="height: 360;"></div> 
                    </th>
                </table>
            </div>                        
            
        </div>
                    
    </div>

    <br><hr><br>  
   
    <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Resumen Técnico"; ?> </label><!--titulo principal de mi modulo-->
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        
        <div class="col-md-6"><!-- inicio del div de la tarjeta de tamaño 8 -->
            
            <div class="card1 mb"><!-- div de la carta principal donde van a ir la informacion ---( me pone la tarjeta)-------------------> 
        
                <table id="myTablee" class="table table-hover table-bordered" style="margin-top:20px" ><!--Titulo de la tabla no se muestra-->
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Resumen Técnico') ?></label></caption><!--Titulo de la tabla si se muestra-->
                    <thead><!--Emcabezados de la tabla -->
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Tecnico') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Programa') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Cliente') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Cantidad de alertas') ?></label></th>

                    </thead>
                
                    
                    <tbody>
                            <?php foreach ($detalleLiderFeedback as $value) : ?>
                                <tr>
                                    <td><?= $value["Tecnico"]; ?></td>
                                    <td><?= $value["Programa"]; ?></td>
                                    <td><?= $value["Cliente"]; ?></td>
                                    <td><?= $value["Count"]; ?></td>
                                </tr>
                            <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="col-md-6">
            <div class="card12 mb"><!-- div de la carta principal donde van a ir la informacion ---( me pone la tarjeta)-------------------> 
    
                <table style="width:100%">
                    <th scope="col" class="text-center" style="width: 100px;">
                            <label style="font-size: 20px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #1993a5;"></em> <?= Yii::t('app', 'Grafica Tipo de Cliente') ?></label><br>
                            <div id="chartContainerClientes" class="highcharts-container" style="height: 650px;"></div> 
                    </th>
                </table>
                
            </div><!--fin de la tarjeta dond esta la tabla -->
        </div>
    </div>
    <br><hr><br>

                
    <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
            <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Vista Global"; ?> </label><!--titulo principal de mi modulo-->
            </div>
        </div>
    </div>
<?php
	if($variables == "70" || $variables == "415" || $variables == "7" || $variables == "438" || $variables == "991" || $variables == "1609" ||
		$variables == "1796" || $variables == "223" || $variables == "556" || $variables == "1251" || $variables == "790" || $variables == "473" ||
			$variables == "777" || $variables == "2953" || $variables == "3229" || $variables == "3468" || $variables == "2913" || $variables == "2911" || $variables == "2991" || $variables == "2990" || $variables == "57" ){
?>
    <<div class="row">  
        <div class="col-md-12"><!-- inicio del div de la tarjeta de tamaño 8 -->
            
            <div class="card1 mb"><!-- div de la carta principal donde van a ir la informacion ---( me pone la tarjeta)-------------------> 
        
                <table id="myTablee3" class="table table-hover table-bordered" style="margin-top:20px" ><!--Titulo de la tabla no se muestra-->
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Global') ?></label></caption><!--Titulo de la tabla si se muestra-->
                    <thead><!--Emcabezados de la tabla -->
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Fecha') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Servicio / PCRC') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Valorador') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Detalle') ?></label></th>

                    </thead>
                
                    
                    <tbody>
                        <?php foreach ($dataProvider as $value) : ?>
                            <tr>
                                <td><?= $value["fecha"]; ?></td>
                                <td><?= $value["Programa"]; ?></td>
                                <td><?= $value["Tecnico"]; ?></td>
                                <td><?= $value["tipo_alerta"]; ?></td>
                                <td><a href="veralertas/<?php echo $value["xid"] ?>"  href="veralertas/" . <?= $value["xid"]; ?>><img src="../../../web/images/ico-view.png" alt="icon-view"></a></td>
                                <td><input type="image" src="../../../web/images/ico-delete.png" alt="icon-delete" name="imagenes" style="cursor:hand" id="imagenes" onclick="eliminarDato(<?php echo $value["xid"] ?>);" /></td> 
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div> 
    </div>        
    <?php
    }
    else
    {
    ?>
        
    <br>
    <div class="row">   
        <div class="col-md-12"><!-- inicio del div de la tarjeta de tamaño 8 -->
            
            <div class="card1 mb"><!-- div de la carta principal donde van a ir la informacion ---( me pone la tarjeta)-------------------> 
        
                <table id="myTablee3" class="table table-hover table-bordered" style="margin-top:20px" ><!--Titulo de la tabla no se muestra-->
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Global') ?></label></caption><!--Titulo de la tabla si se muestra-->
                    <thead><!--Emcabezados de la tabla -->
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Fecha') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Servicio / PCRC') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Valorador') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Detalle') ?></label></th>
                    </thead>
                
                    
                    <tbody>
                        <?php foreach ($dataProvider as $value) : ?>
                            <tr>
                                <td><?= $value["fecha"]; ?></td>
                                <td><?= $value["Programa"]; ?></td>
                                <td><?= $value["Tecnico"]; ?></td>
                                <td><?= $value["tipo_alerta"]; ?></td>
                                <td><a href="veralertas/<?php echo $value["xid"] ?>"  href="veralertas/" . <?= $value["xid"]; ?>><img src="../../../web/images/ico-view.png" alt="icon-view"></a></td>                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
    <?php
    }
    ?>


    <?php
        if($variables == "70" || $variables == "415" || $variables == "7" || $variables == "438" || $variables == "991" || $variables == "1609" ||
            $variables == "1796" || $variables == "223" || $variables == "556" || $variables == "1251" || $variables == "790" || $variables == "473" ||
                $variables == "777" || $variables == "2953" || $variables == "3229" || $variables == "3468" || $variables == "2913" || $variables == "2911" || $variables == "2991" || $variables == "2990" || $variables == "57" ){
    ?>
        
    <div class="row" hidden="hidden">  
        <div class="col-md-12"><!-- inicio del div de la tarjeta de tamaño 8 -->
            
            <div class="card1 mb"><!-- div de la carta principal donde van a ir la informacion ---( me pone la tarjeta)-------------------> 
        
                <table id="myTableOcult" hidden="hidden" class="table table-hover table-bordered" style="margin-top:20px" ><!--Titulo de la tabla no se muestra-->
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Global') ?></label></caption><!--Titulo de la tabla si se muestra-->
                    <thead><!--Emcabezados de la tabla -->
                    <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Fecha') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Servicio / PCRC') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Valorador') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Destinarios') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Asunto') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Respuesta') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Comentario Encuesta') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'ID Encuesta') ?></label></th>

                    </thead>
                
                    
                    <tbody>
                        <?php 
                            foreach ($dataTablaGlobal as $key => $value) { ?>
                            <tr>
                                <td><?= $value["fecha"]; ?></td>
                                <td><?= $value["name"]; ?></td>
                                <td><?= $value["usua_nombre"]; ?></td>
                                <td><?= $value["tipo_alerta"]; ?></td>
                                <td><?= $value["remitentes"]; ?></td>
                                <td><?= $value["asunto"]; ?></td>
                                <td><?= $value["comentario"]; ?></td>
                                <td><?= $value["resp_encuesta_saf"]; ?></td>
                                <td><?= $value["comentario_saf"]; ?></td>
                                <td><?= $value["id_encuesta_saf"]; ?></td>   
                                <td><a href="veralertas/<?php echo $value["xid"] ?>"  href="veralertas/" . <?= $value["xid"]; ?>><img src="../../../web/images/ico-view.png" alt="icon-view"></a></td>
                                <td><input type="image" src="../../../web/images/ico-delete.png" alt="icon-delete" name="imagenes" style="cursor:hand" id="imagenes" onclick="eliminarDato(<?php echo $value["xid"] ?>);" /></td> 
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div> 
    </div>        
    <?php
    }
    else
    {
    ?>
        
    <br>
    <div class="row" hidden="hidden" >   
        <div class="col-md-12"><!-- inicio del div de la tarjeta de tamaño 8 -->
            
            <div class="card1 mb"><!-- div de la carta principal donde van a ir la informacion ---( me pone la tarjeta)-------------------> 
        
                <table id="myTableOcult"  hidden="hidden"  class="table table-hover table-bordered" style="margin-top:20px" ><!--Titulo de la tabla no se muestra-->
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Global') ?></label></caption><!--Titulo de la tabla si se muestra-->
                    <thead><!--Emcabezados de la tabla -->
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Fecha') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Servicio / PCRC') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Valorador') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Destinarios') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Asunto') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Respuesta') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Comentario Encuesta') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'ID Encuesta') ?></label></th>
                    </thead>
                
                    
                    <tbody>
                    <?php  foreach ($dataTablaGlobal as $key => $value) {
                        
                    ?> 
                            <tr>
                                <td><?= $value["fecha"]; ?></td>
                                <td><?= $value["name"]; ?></td>
                                <td><?= $value["usua_nombre"]; ?></td>
                                <td><?= $value["tipo_alerta"]; ?></td>
                                <td><?= $value["remitentes"]; ?></td>
                                <td><?= $value["asunto"]; ?></td>
                                <td><?= $value["comentario"]; ?></td>
                                <td><?= $value["resp_encuesta_saf"]; ?></td>
                                <td><?= $value["comentario_saf"]; ?></td>
                                <td><?= $value["id_encuesta_saf"]; ?></td>                           
                                <td><a href="veralertas/<?php echo $value["id"] ?>"  href="veralertas/" . <?= $value["id"]; ?>><img src="../../../web/images/ico-view.png" alt="icon-view"></a></td>                        </tr>
                        <?php  } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
    <?php
    }
    ?>

    <br>

    <br><hr><br>
</div>


<script type="text/javascript">

function eliminarDato(params1){
	var cajaNum = params1;

    var opcion = confirm("Confirmar la eliminación de la alerta");

    if (opcion == true){
	 $.ajax({
                method: "post",
		url: "pruebas",
                data : {
                    alertas_cx: cajaNum,
                },
                success : function(response){ 
			console.log(response);
			var respuesta = JSON.parse(response);
			console.log(respuesta);
			if(respuesta == 1){
				window.location.href = "../basesatisfaccion/alertasvaloracion";

			}else{
				alert("Error al intentar eliminar la alerta");
			}
                }
            });
    }
};

var xport = {
  _fallbacktoCSV: true,  
  toXLS: function(tableId, filename) {   
    this._filename = (typeof filename == 'undefined') ? tableId : filename;
    
    //var ieVersion = this._getMsieVersion();
    //Fallback to CSV for IE & Edge
    if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
      return this.toCSV(tableId);
    } else if (this._getMsieVersion() || this._isFirefox()) {
      alert("Not supported browser");
    }

    //Other Browser can download xls
    var htmltable = document.getElementById(tableId);
    var html = htmltable.outerHTML;

    this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls'); 
  },
  toCSV: function(tableId, filename) {
    this._filename = (typeof filename === 'undefined') ? tableId : filename;
    // Generate our CSV string from out HTML Table
    var csv = this._tableToCSV(document.getElementById(tableId));
    // Create a CSV Blob
    var blob = new Blob([csv], { type: "text/csv" });

    // Determine which approach to take for the download
    if (navigator.msSaveOrOpenBlob) {
      // Works for Internet Explorer and Microsoft Edge
      navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
    } else {      
      this._downloadAnchor(URL.createObjectURL(blob), 'csv');      
    }
  },
  _getMsieVersion: function() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      // IE 10 or older => return version number
      return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
    }

    var trident = ua.indexOf("Trident/");
    if (trident > 0) {
      // IE 11 => return version number
      var rv = ua.indexOf("rv:");
      return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
    }

    var edge = ua.indexOf("Edge/");
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
    }

    // other browser
    return false;
  },
  _isFirefox: function(){
    if (navigator.userAgent.indexOf("Firefox") > 0) {
      return 1;
    }
    
    return 0;
  },
  _downloadAnchor: function(content, ext) {
      var anchor = document.createElement("a");
      anchor.style = "display:none !important";
      anchor.id = "downloadanchor";
      document.body.appendChild(anchor);

      // If the [download] attribute is supported, try to use it
      
      if ("download" in anchor) {
        anchor.download = this._filename + "." + ext;
      }
      anchor.href = content;
      anchor.click();
      anchor.remove();
  },
  _tableToCSV: function(table) {
    // We'll be co-opting `slice` to create arrays
    var slice = Array.prototype.slice;

    return slice
      .call(table.rows)
      .map(function(row) {
        return slice
          .call(row.cells)
          .map(function(cell) {
            return '"t"'.replace("t", cell.textContent);
          })
          .join(",");
      })
      .join("\r\n");
  }
};

	$.ajaxSetup({
        data: <?= \yii\helpers\Json::encode([
            \yii::$app->request->csrfParam => \yii::$app->request->csrfToken,
        ]) ?>
    });

</script>