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

$this->title = 'Hoja de Vida - Pais & Ciudad';
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

</style>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
    ]); ?>

<div class="capaPrincipal" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <label style="font-size: 15px;"><em class="fas fa-square" style="font-size: 15px; color: #FFC72C;"></em> Complementos: </label> 

            <div class="row">
                <div class="col-md-6">
                    <label style="font-size: 15px;"> Estado Civil: </label>
                    <?=  $form->field($model, 'hv_idcivil', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HojavidaDatacivil::find()->distinct()->where("anulado = 0")->orderBy(['hv_idcivil'=> SORT_ASC])->all(), 'hv_idcivil', 'estadocivil'),
                                          [
                                              'prompt'=>'Seleccionar...',                                              
                                          ]
                              )->label(''); 
                    ?>
                </div>

                <div class="col-md-6">
                    <label style="font-size: 15px;"> Estilo Social: </label>
                    <?=  $form->field($model, 'idestilosocial', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvEstilosocial::find()->distinct()->where("anulado = 0")->orderBy(['idestilosocial'=> SORT_ASC])->all(), 'idestilosocial', 'estilosocial'),
                                          [
                                              'prompt'=>'Seleccionar...',                                              
                                          ]
                              )->label(''); 
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label style="font-size: 15px;"> Dominancia Cerebral: </label>
                    <?=  $form->field($model, 'iddominancia', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvDominancias::find()->distinct()->where("anulado = 0")->orderBy(['iddominancia'=> SORT_ASC])->all(), 'iddominancia', 'dominancia'),
                                          [
                                              'prompt'=>'Seleccionar...',                                              
                                          ]
                              )->label(''); 
                    ?>
                </div>

                <div class="col-md-6">
                    <label style="font-size: 15px;"> Hobbies: </label>
                    <?=  $form->field($model, 'idhobbies', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvHobbies::find()->distinct()->orderBy(['id'=> SORT_ASC])->all(), 'id', 'text'),
                                          [
                                              'prompt'=>'Seleccionar...',                                              
                                          ]
                              )->label(''); 
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label style="font-size: 15px;"> Intereses/Gustos: </label>
                    <?=  $form->field($model, 'idgustos', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvGustos::find()->distinct()->orderBy(['id'=> SORT_ASC])->all(), 'id', 'text'),
                                          [
                                              'prompt'=>'Seleccionar...',               
                                          ]
                              )->label(''); 
                    ?>
                </div>

                <div class="col-md-6">
                    <label style="font-size: 15px;"> Cantidad de hijos: </label>
                    <?= $form->field($model, 'cantidadhijos', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 2, 'id'=>'idrol', 'placeholder'=>'Ingresar en numeros la cantidad de hjos'])?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <label style="font-size: 15px;"> Nombre de los hijos: </label>
                    <?= $form->field($model, 'NombreHijos', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 250, 'id'=>'idrol', 'placeholder'=>'Ingresar el nombre de los hijos'])?>
                    <label style="font-size: 10px;"> Escriba los nombres de los hijos separado con comas ( , ) en caso tal tenga </label>
                    <?= $form->field($model, 'hv_idpersonal', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['class'=> 'hidden', 'value'=>$idsinfo])?>
                </div>
            </div>
            

        </div>
    </div>
</div>
<hr>
<div class="capaBtn" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <?= Html::submitButton('Actualizar Permisos', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>'btn_submit'] ) ?> 
            </div>
        </div>
    </div>
</div>

<?php $form->end() ?>
