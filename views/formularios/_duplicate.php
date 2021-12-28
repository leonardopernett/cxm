<?php

use yii\helpers\Html;

use yii\bootstrap\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Formularios */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Duplicate Formularios');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Formularios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="formularios-create">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <div class="formularios-form">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
              ]
            ]); ?>

        <?= $form->field($model, 'name')->textInput(['id'=>'idname','maxlength' => 100]) ?>    

        <?php //$form->field($model, 'nmorden')->textInput()  ?>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <?=
                Html::submitButton(Yii::t('app', 'Duplicate'),
                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'onclick' => 'validar()'])
                ?>
                <?=
                Html::a(Yii::t('app', 'Cancel'), ['index'],
                        ['class' => 'btn btn-default'])
                ?>
            </div>        
        </div>

<?php ActiveForm::end(); ?>

    </div>

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