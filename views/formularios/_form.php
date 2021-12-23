<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Formularios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="formularios-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?= $form->field($model, 'name')->textInput(['id'=>'idname']) ?>
    <?= $form->field($model, 'id_plantilla_form')->dropDownList(Yii::$app->params["lista_plantilla"]) ?>

    <?php
    echo $form->field($model, 'subi_calculo')->widget(Select2::classname(), [
        'language' => 'es',
        'name' => 'subi_calculo',
        'options' => [
            'placeholder' => Yii::t('app', 'Select ...'),
            'id' => 'subi_calculo'
        ],
        'pluginOptions' => [
            'multiple' => true,
            'allowClear' => true,
            'minimumInputLength' => 3,
                                            'maximumSelectionSize' => 5,

            'ajax' => [
                'url' => \yii\helpers\Url::to(['metricalistmultiple']),
                'dataType' => 'json',
                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
            ],
            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['metricalistmultiple']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
        ]
    ]);
    ?>

    <div class="col-md-6">
        <strong><?= Yii::t('app', 'formularios_msg1') ?></strong>  
        <br/><br/>
        <?= $form->field($model, 'i1_cdtipo_eval', ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
        ?>

        <?= $form->field($model, 'i2_cdtipo_eval', ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
        ?>

        <?= $form->field($model, 'i3_cdtipo_eval', ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
        ?>

        <?= $form->field($model, 'i4_cdtipo_eval', ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
        ?>

        <?= $form->field($model, 'i5_cdtipo_eval', ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
        ?>

        <?= $form->field($model, 'i6_cdtipo_eval', ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
        ?>

        <?= $form->field($model, 'i7_cdtipo_eval', ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
        ?>

        <?= $form->field($model, 'i8_cdtipo_eval', ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
        ?>

        <?= $form->field($model, 'i9_cdtipo_eval', ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
        ?>

        <?= $form->field($model, 'i10_cdtipo_eval', ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
        ?>
    </div>

    <div class="col-md-6">
        <strong><?= Yii::t('app', 'formularios_msg2') ?></strong>
        <br/><br/>
        <?= $form->field($model, 'i1_nmfactor', ['labelOptions' => ['class' => 'col-md-6']])->textInput()
        ?>

        <?= $form->field($model, 'i2_nmfactor', ['labelOptions' => ['class' => 'col-md-6']])->textInput()
        ?>

        <?= $form->field($model, 'i3_nmfactor', ['labelOptions' => ['class' => 'col-md-6']])->textInput()
        ?>

        <?= $form->field($model, 'i4_nmfactor', ['labelOptions' => ['class' => 'col-md-6']])->textInput()
        ?>

        <?= $form->field($model, 'i5_nmfactor', ['labelOptions' => ['class' => 'col-md-6']])->textInput()
        ?>

        <?= $form->field($model, 'i6_nmfactor', ['labelOptions' => ['class' => 'col-md-6']])->textInput()
        ?>

        <?= $form->field($model, 'i7_nmfactor', ['labelOptions' => ['class' => 'col-md-6']])->textInput()
        ?>

        <?= $form->field($model, 'i8_nmfactor', ['labelOptions' => ['class' => 'col-md-6']])->textInput()
        ?>

        <?= $form->field($model, 'i9_nmfactor', ['labelOptions' => ['class' => 'col-md-6']])->textInput()
        ?>

        <?= $form->field($model, 'i10_nmfactor', ['labelOptions' => ['class' => 'col-md-6']])->textInput()
        ?>
    </div>

    <?php //$form->field($model, 'nmorden')->textInput()  ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
            'onclick' => 'validar()'])
            ?>
            <?=
            Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default'])
            ?>
        </div>        
    </div>

<?php ActiveForm::end(); ?>

<script>

function validar(){
    var varidname = document.getElementById("idname").value;


   if(varidname === ""){
       
    swal.fire("Nombre no puede estar vacio")
    return;
   }else if(varidname.length>100){
       
       swal.fire("Solo se permiten 100 caracteres")
       return;
      }



}


</script>


</div>
