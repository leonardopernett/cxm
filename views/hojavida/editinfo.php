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
use app\models\ControlProcesosPlan;
use yii\db\Query;

$this->title = 'Hoja de Vida - Data Personal';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sesiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();


    $varRespuesta = ['1' => 'No', '2' => 'Si'];
    $varAfinidad = ['1' => 'Relación Directa', '2' => 'Relación de Interés'];
    $varTipo = ['1' => 'Decisor', '2' => 'No Decisor'];
    $varNivel = ['1' => 'Estratégico', '2' => 'Operativo'];
    $varEstado = ['1' => 'Activo', '2' => 'No Activo'];
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
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<div id="idcapauno" class="capaPrincipal" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-info" style="font-size: 15px; color: #FFC72C;"></em> Datos Personales </label>

        <div class="row">          
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Nombre Completo: </label>
            <?= $form->field($model, 'nombre_full', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idnombrefull', 'placeholder'=>'Ingresar Nombres y Apellidos'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> N&uacute;mero de Identidad: </label>
            <?= $form->field($model, 'identificacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'ididentificacion', 'placeholder'=>'Ingresar Documento de Identidad','onkeypress' => 'return valida(event)'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Correo Electr&oacute;nico Corporativo: </label>
            <?= $form->field($model, 'email', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idemail', 'placeholder'=>'Ingresar Correo Corportativo'])?>
          </div>
        </div>

        <div class="row">          
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> N&uacute;mero Celular: </label>
            <?= $form->field($model, 'numero_movil', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idnumeromovil', 'placeholder'=>'Ingresar Número Celular','onkeypress' => 'return valida(event)'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"> T&eacute;lefono Oficina: </label>
            <?= $form->field($model, 'numero_fijo', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idnumerooficina', 'placeholder'=>'Ingresar Número Oficina','onkeypress' => 'return valida(event)'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Modalidad de Trabajo: </label>
            <?=  $form->field($model, 'hv_idmodalidad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvModalidadTrabajo::find()->orderBy(['hv_idmodalidad'=> SORT_DESC])->all(), 'hv_idmodalidad', 'modalidad'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>
        </div>

        <div class="row">          
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span>Direcci&oacute;n Oficina: </label>
            <?= $form->field($model, 'direccion_oficina', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'iddireccionoficiona', 'placeholder'=>'Ingresar Número Celular'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Direcci&oacute;n Domicilio: </label>
            <?= $form->field($model, 'direccion_casa', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'iddireccioncasa', 'placeholder'=>'Ingresar Número Oficina'])?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Autoriza el Tratamiento de Datos Personales: </label>
            <?= $form->field($model, "tratamiento_data", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varRespuesta, ['prompt' => 'Seleccionar...', 'id'=>"idautoriza"]) ?>
          </div>
        </div>

        <div class="row">          
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Pa&iacute;s: </label>
            <?=  $form->field($model, 'hv_idpais', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvPais::find()->distinct()->orderBy(['pais'=> SORT_ASC])->all(), 'hv_idpais', 'pais'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                            'onchange' => '
                                                $.get(
                                                    "' . Url::toRoute('listarciudades') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        $("#idciudad").html(res);
                                                    }
                                                );
                                            ',

                                        ]
                            )->label(''); 
            ?>
          </div>

          <div  class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Ciudad: </label>
            <?= $form->field($model,'hv_idciudad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar...',
                                            'id' => 'idciudad'
                                        ]
                                    )->label('');
            ?>
          </div>

          <div  class="col-md-4">

            <div class="row">
              <div class="col-md-6">
                <label style="font-size: 15px;"> Es Susceptibe a Encuestar: </label>
                <?= $form->field($model, "suceptible", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varRespuesta, ['prompt' => 'Seleccionar...', 'id'=>"idsusceptible"]) ?>
              </div>

              <div class="col-md-6">
                <label style="font-size: 15px;"> Indicador Satu: (%)</label>
                <?= $form->field($model, 'indicador_satu', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idsatu', 'placeholder'=>'Ingresar Porcentaje Satu', 'type'=>'number', 'onkeypress' => 'return valida(event)'])?>
              </div>
            </div>            
            
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"> Clasificación Ciudad Konecta</label>
            <?=  $form->field($model, 'clasificacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HojavidaDataclasificacion::find()->distinct()->orderBy(['hv_idclasificacion'=> SORT_ASC])->all(), 'hv_idclasificacion', 'ciudadclasificacion'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                            )->label(''); 
            ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<hr>

<div id="idcapados" class="capaDos" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-plus-square" style="font-size: 15px; color: #FFC72C;"></em> Datos Laborales </label>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Rol: </label>
            <?= $form->field($model2, 'rol', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idrol', 'placeholder'=>'Ingresar El Rol'])?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Antiguedad del Rol: </label>
            <?=  $form->field($model2, 'hv_id_antiguedad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvAntiguedadRol::find()->orderBy(['hv_id_antiguedad'=> SORT_DESC])->all(), 'hv_id_antiguedad', 'antiguedad'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"> Fecha Inicio Como Contacto: </label>

            <?= $form->field($model2, 'fecha_inicio_contacto', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->widget(\yii\jui\DatePicker::className(), ['dateFormat' => 'yyyy-MM-dd','options' => ['class' => 'form-control','readonly' => 'readonly', 'id'=>'idfechainicio'],]) ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"> Nombre del Jefe: </label>
            <?= $form->field($model2, 'nombre_jefe', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idnombrejefe', 'placeholder'=>'Ingresar Nombre del Jefe'])?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"> Cargo del Jefe: </label>
            <?= $form->field($model2, 'cargo_jefe', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idcargojefe', 'placeholder'=>'Ingresar Cargo del Jefe'])?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"> Trabajo Anterior: </label>
            <?= $form->field($model2, 'trabajo_anterior', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idtrabajoanterior', 'placeholder'=>'Ingresar Trabajo Anterior'])?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Afinidad: </label>
            <?= $form->field($model2, "afinidad", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varAfinidad, ['prompt' => 'Seleccionar...', 'id'=>"idafinidad", 'onchange' => 'habilitar();']) ?>
          </div>

          <div id="idafinidades" style="display: none;">
            <div class="col-md-4">
              <label style="font-size: 15px;"> Tipo Afinidad: </label>
              <?= $form->field($model2, "tipo_afinidad", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varTipo, ['prompt' => 'Seleccionar...', 'id'=>"idtipoafinidad"]) ?>
            </div>

            <div class="col-md-4">
              <label style="font-size: 15px;"> Nivel Afinidad: </label>
              <?= $form->field($model2, "nivel_afinidad", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varNivel, ['prompt' => 'Seleccionar...', 'id'=>"idnivelafinidad"]) ?>
            </div>
          </div>          
        </div>

      </div>
    </div>
  </div>
</div>
<hr>

<div id="idcapatres" class="capaTres" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-check-circle" style="font-size: 15px; color: #FFC72C;"></em> Datos de la Cuenta </label>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Cliente: </label>
            <?php
              $paramsidcliente = [':idhvaccion' => $idinfo];
              $varIdCliente = Yii::$app->db->createCommand('
                SELECT dc.id_dp_cliente FROM tbl_hojavida_datapcrc dc
                  INNER JOIN tbl_hojavida_datapersonal dp ON 
                    dc.hv_idpersonal = dp.hv_idpersonal
                  WHERE
                    dp.hv_idpersonal = :idhvaccion
                  GROUP BY dc.id_dp_cliente')->bindValues($paramsidcliente)->queryScalar();
            ?>
            <?=  $form->field($model4, 'id_dp_cliente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->andwhere("id_dp_clientes = $varIdCliente")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                          [
                                              'prompt'=>'Seleccionar...',
                                              'onchange' => '
                                                  $.post(
                                                      "' . Url::toRoute('formulariovoc/listarpcrcindex') . '", 
                                                      {id: $(this).val()}, 
                                                      function(res){
                                                          $("#requester").html(res);
                                                      }
                                                  );

                                                  $.get(
                                                      "' . Url::toRoute('hojavida/listardirectores') . '", 
                                                      {id: $(this).val()}, 
                                                      function(res){
                                                          $("#requester2").html(res);
                                                      }
                                                  );

                                                  $.get(
                                                      "' . Url::toRoute('hojavida/listargerentes') . '", 
                                                      {id: $(this).val()}, 
                                                      function(res){
                                                          $("#requester3").html(res);
                                                      }
                                                  );
                                              ',

                                          ]
                              )->label(''); 
            ?>
          </div>

          <div class="col-md-8">
            <div class="panel panel-default">
              <div class="panel-body">
                <label style="font-size: 13px;"> Ten presente, para seleccionar varias opciones, es necesario seleccionar el cliente y luego mantener presionado la tecla Ctrl y seleccionas con clic los items que necesites.</label>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Programa Pcrc: </label>
            <?= $form->field($model4,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar...',
                                            'id' => 'requester',
                                            'multiple' => true,
                                        ]
                                    )->label('');
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Director: </label>
            <?= $form->field($model5,'ccdirector', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar...',
                                            'id' => 'requester2',
                                            'multiple' => true,
                                        ]
                                    )->label('');
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> Gerente: </label>
            <?= $form->field($model6,'ccgerente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar...',
                                            'id' => 'requester3',
                                            'multiple' => true,
                                        ]
                                    )->label('');
            ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
              <table id="tblDataCuentaspcrc" class="table table-striped table-bordered tblResDetFreed">
                <caption><?php echo "Resultados Pcrc"; ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Programa Pcrc') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $paramscliente = [':idhvaccion' => $idinfo];
                    $varListaPcrc = Yii::$app->db->createCommand('
                    SELECT dc.hv_idpcrc, CONCAT(cc.cod_pcrc," - ",cc.pcrc) AS Pcrc
                       FROM tbl_hojavida_datapcrc dc 
                          INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                            dc.cod_pcrc = cc.cod_pcrc
                          WHERE dc.hv_idpersonal = :idhvaccion
                            GROUP BY cc.cod_pcrc')->bindValues($paramscliente)->queryAll();

                    foreach ($varListaPcrc as $key => $value) {
                      
                  ?>             
                    <tr>
                      <td><label style="font-size: 12px;"><?php echo  $value['Pcrc']; ?></label></td>
                      <td class="text-center">
                        <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletepcrc','id'=> $value['hv_idpcrc'], 'idsinfo' =>$idinfo], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                      </td>
                    </tr>
                  <?php
                    }
                  ?>
                </tbody>
              </table>
          </div>

          <div class="col-md-4">
              <table id="tblDataCuentasdirector" class="table table-striped table-bordered tblResDetFreed">
                <caption><?php echo "Resultados Director"; ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Director') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $paramsdirector = [':idhvaccion' => $idinfo];
                    $varListaDirector = Yii::$app->db->createCommand('
                    SELECT dc.hv_iddirector, cc.director_programa
                      FROM tbl_hojavida_datadirector dc 
                        INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                          dc.ccdirector = cc.documento_director
                        WHERE dc.hv_idpersonal = :idhvaccion
                          GROUP BY cc.documento_director')->bindValues($paramsdirector)->queryAll();

                    foreach ($varListaDirector as $key => $value) {
                      
                  ?>             
                    <tr>
                      <td><label style="font-size: 12px;"><?php echo  $value['director_programa']; ?></label></td>
                      <td class="text-center">
                        <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletedirector','id'=> $value['hv_iddirector'], 'idsinfo' =>$idinfo], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                      </td>
                    </tr>
                  <?php
                    }
                  ?>
                </tbody>
              </table>
          </div>

          <div class="col-md-4">
              <table id="tblDataCuentasgerente" class="table table-striped table-bordered tblResDetFreed">
                <caption><?php echo "Resultados Director"; ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Gerente') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $paramsgerente = [':idhvaccion' => $idinfo];
                    $varListaGerente = Yii::$app->db->createCommand('
                    SELECT dc.hv_idgerente, cc.gerente_cuenta
                     FROM tbl_hojavida_datagerente dc 
                        INNER JOIN tbl_proceso_cliente_centrocosto cc ON 
                          dc.ccgerente = cc.documento_gerente
                        WHERE dc.hv_idpersonal = :idhvaccion
                          GROUP BY cc.documento_gerente')->bindValues($paramsgerente)->queryAll();

                    foreach ($varListaGerente as $key => $value) {
                      
                  ?>             
                    <tr>
                      <td><label style="font-size: 12px;"><?php echo  $value['gerente_cuenta']; ?></label></td>
                      <td class="text-center">
                        <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletegerente','id'=> $value['hv_idgerente'], 'idsinfo' =>$idinfo], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                      </td>
                    </tr>
                  <?php
                    }
                  ?>
                </tbody>
              </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<hr>

<div id="idcapacuatro" class="capaCuatro" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-plus" style="font-size: 15px; color: #FFC72C;"></em> Datos Acad&eacute;micos </label>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"> Profesi&oacute;n: </label>
            <?=  $form->field($model3, 'idhvcursosacademico', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvCursosacademico::find()->where("idhvacademico = 4")->orderBy(['idhvcursosacademico'=> SORT_DESC])->all(), 'idhvcursosacademico', 'hv_cursos'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"> Especializaci&oacute;n: </label>
            <?=  $form->field($model3, 'anulado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvCursosacademico::find()->where("idhvacademico = 2")->orderBy(['idhvcursosacademico'=> SORT_DESC])->all(), 'idhvcursosacademico', 'hv_cursos'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"> Maestr&iacute;a: </label>
            <?=  $form->field($model3, 'hv_idpersonal', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvCursosacademico::find()->where("idhvacademico = 3")->orderBy(['idhvcursosacademico'=> SORT_DESC])->all(), 'idhvcursosacademico', 'hv_cursos'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"> Doctorado: </label>
            <?=  $form->field($model3, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvCursosacademico::find()->where("idhvacademico = 1")->orderBy(['idhvcursosacademico'=> SORT_DESC])->all(), 'idhvcursosacademico', 'hv_cursos'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                        ]
                                )->label(''); 
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"> Estado: </label>
            <?= $form->field($model3, "activo", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varEstado, ['prompt' => 'Seleccionar...', 'id'=>"idestado"]) ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            
              <table id="tblDataAcademico" class="table table-striped table-bordered tblResDetFreed">
                <caption><?php echo "Resultados"; ?></caption>
                <tbody>
                  <?php 
                    $paramsacademy = [':idhvaccion' => $idinfo];
                    $varListaAcademica = Yii::$app->db->createCommand('
                      SELECT da.hv_idacademica, n.hvacademico AS Nivel, c.hv_cursos AS Cursos FROM tbl_hv_nivelacademico n 
                          INNER JOIN tbl_hv_cursosacademico c ON  
                            n.idhvacademico = c.idhvacademico
                          INNER JOIN tbl_hojavida_dataacademica da ON 
                            c.idhvcursosacademico = da.idhvcursosacademico
                          WHERE 
                            da.hv_idpersonal = :idhvaccion')->bindValues($paramsacademy)->queryAll();

                    foreach ($varListaAcademica as $key => $value) {              
                    
                  ?>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', $value['Nivel']) ?></label></th>
                    <td><label style="font-size: 12px;"><?php echo  $value['Cursos']; ?></label></td>
                    <td class="text-center">
                      <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deleteacademico','id'=> $value['hv_idacademica'], 'idsinfo' =>$idinfo], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                    </td>
                  </tr>
                  <?php
                    }
                  ?>
                </tbody>
              </table>
            
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<hr>

<div class="capaCinco" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 15px; color: #FFC72C;"></em> Eventos: </label> 

        <?=  $form->field($model7, 'hv_ideventos', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HojavidaEventos::find()->where("anulado = 0")->orderBy(['hv_ideventos'=> SORT_DESC])->all(), 'hv_ideventos', 'nombre_evento'),
                                        [
                                            'prompt'=>'Seleccionar...',
                                            'multiple' => true,
                                        ]
                                )->label(''); 
        ?>

        <br>

              <table id="tblDataEventos" class="table table-striped table-bordered tblResDetFreed">
                <caption><?php echo "Resultados Eventos"; ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Evento') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Evento') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ciudad Evento') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Evento') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                      $paramsevento = [':idhvaccion' => $idinfo];
                      $varListaEventos = Yii::$app->db->createCommand('
                        SELECT ha.hv_idasignareventos, he.nombre_evento, he.tipo_evento, c.ciudad,
                          he.fecha_evento_inicio FROM tbl_hv_ciudad c
                            INNER JOIN tbl_hojavida_eventos he ON 
                              c.hv_idciudad = he.hv_idciudad
                            INNER JOIN tbl_hojavida_asignareventos ha ON 
                              he.hv_ideventos = ha.hv_ideventos
                            INNER JOIN tbl_hojavida_datapersonal hd ON 
                              ha.hv_idpersonal = hd.hv_idpersonal
                            WHERE 
                              hd.hv_idpersonal = :idhvaccion')->bindValues($paramsevento)->queryAll();

                      foreach ($varListaEventos as $key => $value) { 
                      
                  ?>             
                    <tr>
                      <td><label style="font-size: 12px;"><?php echo  $value['nombre_evento']; ?></label></td>
                      <td><label style="font-size: 12px;"><?php echo  $value['tipo_evento']; ?></label></td>
                      <td><label style="font-size: 12px;"><?php echo  $value['ciudad']; ?></label></td>
                      <td><label style="font-size: 12px;"><?php echo  $value['fecha_evento_inicio']; ?></label></td>
                      <td class="text-center">
                        <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deleteeventos','id'=> $value['hv_idasignareventos'], 'idsinfo' =>$idinfo], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                      </td>
                    </tr>
                  <?php
                    }
                  ?>
                </tbody>
              </table>
           
      </div>

    </div>
  </div>
</div>
<hr>

<div class="capaCinco" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      
    <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-square" style="font-size: 15px; color: #FFC72C;"></em> Complementos: </label> 

        <?php

          $paramscomplement = [':idhvaccion' => $idinfo];
          $varListaComplementos = Yii::$app->db->createCommand('
                        SELECT dc.hv_idcomplemento, hd.estadocivil, dc.cantidadhijos, dc.NombreHijos, d.dominancia,
                        e.estilosocial, g.text, h.text AS hobbies FROM tbl_hojavida_datacomplementos dc
                          LEFT JOIN tbl_hojavida_datacivil hd ON 
                            dc.hv_idcivil = hd.hv_idcivil
                          LEFT JOIN tbl_hv_dominancias d ON 
                            dc.iddominancia = d.iddominancia
                          LEFT JOIN tbl_hv_estilosocial e ON 
                            dc.idestilosocial = e.idestilosocial
                          LEFT JOIN tbl_hv_gustos g ON 
                            dc.idgustos = g.id
                          LEFT JOIN tbl_hv_hobbies h ON 
                            dc.idhobbies = h.id
                          LEFT JOIN tbl_hojavida_datapersonal dp ON 
                            dc.hv_idpersonal = dp.hv_idpersonal
                          WHERE 
                            dp.hv_idpersonal = :idhvaccion')->bindValues($paramscomplement)->queryAll();

        ?>

        <div class="row">
          <div class="col-md-12">

            <?php if (count($varListaComplementos) == 0) { ?>
              
              <?= 
                  Html::button('Nuevo Complementos', ['value' => url::to(['complementosaccion','idsinfo'=>$idinfo]), 'class' => 'btn btn-success', 'style' => 'background-color: #337ab7', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Nuevo Complementos'])
              ?>
              <?php
                  Modal::begin([
                      'header' => '<h4></h4>',
                      'id' => 'modal1',
                  ]);

                  echo "<div id='modalContent1'></div>";
                                                          
                  Modal::end(); 
              ?> 

            <?php }else{ ?>

              <?= Html::a('Agregar Complementos',  ['complementosadd','idsinfo'=>$idinfo], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab7;", 'title' => 'Agregar Complementos']) ?>

            <?php } ?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
              <table id="tblDataComplementos" class="table table-striped table-bordered tblResDetFreed">
                <caption><?php echo "Resultados Complementos"; ?></caption>
                <thead>
                  <tr>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado Civil') ?></label></th>                    
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Hijos') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Hijos') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estilo Social') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Dominancia Cerebral') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Hobbies') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Gustos') ?></label></th>
                    <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                      $varContador = 2;
                      foreach ($varListaComplementos as $key => $value) { 
                        $varContador += 1;
                  ?>             
                    <tr>
                      <td><label style="font-size: 12px;"><?php echo  $value['estadocivil']; ?></label></td>
                      <td><label style="font-size: 12px;"><?php echo  $value['cantidadhijos']; ?></label></td>
                      <td><label style="font-size: 12px;"><?php echo  $value['NombreHijos']; ?></label></td>
                      <td><label style="font-size: 12px;"><?php echo  $value['dominancia']; ?></label></td>
                      <td><label style="font-size: 12px;"><?php echo  $value['estilosocial']; ?></label></td>
                      <td><label style="font-size: 12px;"><?php echo  $value['text']; ?></label></td>
                      <td><label style="font-size: 12px;"><?php echo  $value['hobbies']; ?></label></td>
                      <td class="text-center">
                        <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletecomplementos','id'=> $value['hv_idcomplemento'], 'idsinfo' =>$idinfo], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                        <?= Html::a('<em class="fas fa-edit" style="font-size: 15px; color: #FC4343;"></em>',  ['editcomplementos','id'=> $value['hv_idcomplemento'], 'idsinfo' =>$idinfo], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Editar']) ?>
                        
                      </td>
                    </tr>
                  <?php
                    }
                  ?>
                </tbody>
              </table>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>
<hr>

<div class="capaBtn" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      
      <div class="row">
        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
            ?>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Guardar Datos: </label>
            <div onclick="updatedata();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  Actualizar Informacion
            </div> 
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<hr>

<?php $form->end() ?>

<script type="text/javascript">
  function valida(e){
    tecla = (document.all) ? e.keyCode : e.which;

    //Tecla de retroceso para borrar, siempre la permite
    if (tecla==8){
      return true;
    }
            
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
  };

  function habilitar(){
    var varidafinidad = document.getElementById("idafinidad").value;
    var varidafinidades = document.getElementById("idafinidades");

    if (varidafinidad == "1") {
      varidafinidades.style.display = 'inline';
    }else{
      varidafinidades.style.display = 'none';
    }
  };

  function updatedata(){
    var varidnombrefull = document.getElementById("idnombrefull").value;
    var varididentificacion = document.getElementById("ididentificacion").value;
    var varidemail = document.getElementById("idemail").value;
    var varidnumeromovil = document.getElementById("idnumeromovil").value;
    var varidnumerooficina = document.getElementById("idnumerooficina").value;
    var varidmdoalidad = document.getElementById("hojavidadatapersonal-hv_idmodalidad").value;
    var variddireccionoficiona = document.getElementById("iddireccionoficiona").value;
    var variddireccioncasa = document.getElementById("iddireccioncasa").value;
    var varidautoriza = document.getElementById("idautoriza").value;
    var varidpais = document.getElementById("hojavidadatapersonal-hv_idpais").value;
    var varididciudad = document.getElementById("idciudad").value;
    var varidsusceptible = document.getElementById("idsusceptible").value;
    var varidsatu = document.getElementById("idsatu").value;
    var varautoincrement = "<?php echo $idinfo; ?>";
    var varclasificacion = document.getElementById("hojavidadatapersonal-clasificacion").value;

    if (varididentificacion == "") {

      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar documento de identidad","warning");
      return;

    }else{
            if (varidnombrefull == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el nombre completo","warning");
              return;
            }
            if (varidemail == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el correo corporativo","warning");
              return;
            }
            if (varidnumeromovil == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el numero movil","warning");
              return;
            }
            if (varidmdoalidad == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la modalidad","warning");
              return;
            }
            if (variddireccionoficiona == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el direccion oficina","warning");
              return;
            }
            if (variddireccioncasa == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresar el direccion casa","warning");
              return;
            }
            if (varidautoriza == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la autorizacion","warning");
              return;
            }
            if (varidpais == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar el pais","warning");
              return;
            }
            if (varididciudad == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la ciudad","warning");
              return;
            }
            if (varclasificacion == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la clasificacion konecta","warning");
              return;
            }

            // Esta accion permite guardar el primer bloque...
            $.ajax({
              method: "get",
              url: "actualizapersonal",
              data: {
                txtvarautoincrement : varautoincrement,
                txtvaridnombrefull: varidnombrefull,
                txtvarididentificacion : varididentificacion,
                txtvaridemail : varidemail,
                txtvaridnumeromovil : varidnumeromovil,
                txtvaridnumerooficina : varidnumerooficina,
                txtvaridmdoalidad : varidmdoalidad,
                txtvariddireccionoficiona : variddireccionoficiona,
                txtvariddireccioncasa : variddireccioncasa,
                txtvaridautoriza : varidautoriza,
                txtvaridpais : varidpais,
                txtvarididciudad : varididciudad,
                txtvaridsusceptible : varidsusceptible,
                txtvaridsatu : varidsatu,
                txtvarclasificacion : varclasificacion,
              },
              success : function(response){
                numRta =   JSON.parse(response);
              }
            });

            var varidrol = document.getElementById("idrol").value;
            var varidantiguedad = document.getElementById("hojavidadatalaboral-hv_id_antiguedad").value;
            var varidfechainicio = document.getElementById("idfechainicio").value;
            var varidnombrejefe = document.getElementById("idnombrejefe").value;
            var varidcargojefe = document.getElementById("idcargojefe").value;
            var varidtrabajoanterior = document.getElementById("idtrabajoanterior").value;
            var varidafinidad = document.getElementById("idafinidad").value;
            var varidtipoafinidad = document.getElementById("idtipoafinidad").value;
            var varidnivelafinidad = document.getElementById("idnivelafinidad").value;

            if (varidrol == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de ingresa el rol","warning");
              return;
            }
            if (varidantiguedad == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la antiguedad","warning");
              return;
            }
            if (varidafinidad == "") {
              event.preventDefault();
              swal.fire("!!! Advertencia !!!","Debe de seleccionar la afinidad","warning");
              return;
            }

            // Esta accion permite guardar el segundo bloque...
            $.ajax({
              method: "get",
              url: "actualizalaboral",
              data: {
                txtvarautoincrement : varautoincrement,
                txtvarididentificacion : varididentificacion,
                txtvaridrol : varidrol,
                txtvaridantiguedad : varidantiguedad,
                txtvaridfechainicio : varidfechainicio,
                txtvaridnombrejefe : varidnombrejefe,
                txtvaridcargojefe : varidcargojefe,
                txtvaridtrabajoanterior : varidtrabajoanterior,
                txtvaridafinidad : varidafinidad,
                txtvaridtipoafinidad : varidtipoafinidad,
                txtvaridnivelafinidad : varidnivelafinidad,
              },
              success : function(response){
                numRta =   JSON.parse(response);
              }
            });
            
            var varid_dp_cliente = document.getElementById("hojavidadatapcrc-id_dp_cliente").value;
            var varidrequester = document.getElementById("requester").value;
            var varcodpcrc = document.querySelectorAll('#requester option:checked');
            var varlistcodpcrc = Array.from(varcodpcrc).map(el => el.value);
            var varidrequester2 = document.getElementById("requester2").value;
            var vardirector = document.querySelectorAll('#requester2 option:checked');
            var varlistdirector = Array.from(vardirector).map(el => el.value);
            var varidrequester3 = document.getElementById("requester3").value;
            var vargerente = document.querySelectorAll('#requester3 option:checked');
            var varlistgerente = Array.from(vargerente).map(el => el.value);

            if (varid_dp_cliente != "") {
              // Esta accion permite guardar el tercer bloque...
              $.ajax({
                method: "get",
                url: "actualizacuentas",
                data: {
                  txtvarautoincrement : varautoincrement,
                  txtvarid_dp_cliente : varid_dp_cliente,
                  txtvaridrequester : varlistcodpcrc,
                  txtvaridrequester2 : varlistdirector,
                  txtvaridrequester3 : varlistgerente,

                },
                success : function(response){
                  numRta =   JSON.parse(response);
                }
              });
            }
            

            var vareventos = document.querySelectorAll('#hojavidadataacademica-usua_id option:checked');
            var varlisteventos = Array.from(vareventos).map(el => el.value);

            // Esta accion permite guardar el tercer bloque...
            if (vareventos != "") {              
              $.ajax({
                method: "get",
                url: "aplicareventos",
                data: {
                  txtvarautoincrement : varautoincrement,
                  txtvarlisteventos : varlisteventos,

                },
                success : function(response){
                  numRta =   JSON.parse(response);
                }
              });
            }


            var varidprofesion = document.getElementById("hojavidadataacademica-idhvcursosacademico").value;
            var varidespecializacion = document.getElementById("hojavidadataacademica-anulado").value;
            var varidmaestria = document.getElementById("hojavidadataacademica-hv_idpersonal").value;
            var variddoctorado = document.getElementById("hojavidadataacademica-usua_id").value;
            var varidestado = document.getElementById("idestado").value;

            // Esta accion permite guardar el cuarto bloque...
            
              $.ajax({
                method: "get",
                url: "guardaracademicos",
                data: {
                  txtvarautoincrement : varautoincrement,
                  txtvaridprofesion : varidprofesion,
                  txtvaridespecializacion : varidespecializacion,
                  txtvaridmaestria : varidmaestria,
                  txtvariddoctorado : variddoctorado,
                  txtvaridestado : varidestado,

                },
                success : function(response){
                  numRta =   JSON.parse(response);
                }
              });
            
            

            window.open('../hojavida/index','_self');
    }

  };
</script>