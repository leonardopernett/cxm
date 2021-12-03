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

$this->title = 'Registro de Categorias DashBoard Speech';

    $template = '<div class="col-md-12">'
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

?>
<div class="capapp" style="display: inline;">
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
    ]); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <div class="row">                    
                    <div class="col-md-6">
                        <label style="font-size: 15px;"> Cliente Speech: </label>
                        <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosVolumendirector::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                                [
                                                    'prompt'=>'Seleccione Cliente Speech...',
                                                    'onchange' => '
                                                        $.post(
                                                            "' . Url::toRoute('dashboardspeech/listarpcrc') . '", 
                                                            {id: $(this).val()}, 
                                                            function(res){
                                                                $("#requester").html(res);
                                                            }
                                                        );
                                                    ',

                                                ]
                                    )->label(''); 
                        ?>  
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;"> Centro de Costos: </label>
                        <?= $form->field($model,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                            [],
                                            [
                                                'prompt' => 'Seleccione Centro de Costos...',
                                                'id' => 'requester'
                                            ]
                                        )->label('');
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;"> Regla de Negocio: </label>
                        <?= $form->field($model, 'rn', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'id'=>'idRN'])->label('') ?> 
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;"> Extensión: </label>
                        <?= $form->field($model, 'ext', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'id'=>'idExt'])->label('') ?>
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;"> Usuario de Red: </label>
                         <?= $form->field($model, 'usuared', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'id'=>'idRed'])->label('') ?>
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;"> Otros: </label>
                         <?= $form->field($model, 'comentarios', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'id'=>'idTXT'])->label('') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= Html::submitButton(Yii::t('app', 'Guardar Parametros'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                            'data-toggle' => 'tooltip',
                            'title' => 'Guardar Parametros']) 
                        ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>

