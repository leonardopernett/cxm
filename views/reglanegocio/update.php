<?php

use yii\helpers\Html;
$this->registerJsFile('@web/js/bootstrap-tagsinput.js');
/* @var $this yii\web\View */
/* @var $model app\models\Reglanegocio */

$this->title = Yii::t('app', 'Update Reglanegocio');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reglanegocios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="reglanegocio-update">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>


<script type="text/javascript">

    $(document).ready(function () {
        
            $("[id*=reglanegocio-tramo]").change(function() {
            var sumatot = parseFloat($('#reglanegocio-tramo1').val()) + parseFloat($('#reglanegocio-tramo2').val()) + parseFloat($('#reglanegocio-tramo3').val()) + parseFloat($('#reglanegocio-tramo4').val()) + parseFloat($('#reglanegocio-tramo5').val()) + parseFloat($('#reglanegocio-tramo6').val()) + parseFloat($('#reglanegocio-tramo7').val()) + parseFloat($('#reglanegocio-tramo8').val()) + parseFloat($('#reglanegocio-tramo9').val()) + parseFloat($('#reglanegocio-tramo10').val()) + parseFloat($('#reglanegocio-tramo11').val()) + parseFloat($('#reglanegocio-tramo12').val()) + parseFloat($('#reglanegocio-tramo13').val()) + parseFloat($('#reglanegocio-tramo14').val()) + parseFloat($('#reglanegocio-tramo15').val()) + parseFloat($('#reglanegocio-tramo16').val()) + parseFloat($('#reglanegocio-tramo17').val()) + parseFloat($('#reglanegocio-tramo18').val()) + parseFloat($('#reglanegocio-tramo19').val()) + parseFloat($('#reglanegocio-tramo20').val()) + parseFloat($('#reglanegocio-tramo21').val()) + parseFloat($('#reglanegocio-tramo22').val()) + parseFloat($('#reglanegocio-tramo23').val()) + parseFloat($('#reglanegocio-tramo24').val());

            $('#reglanegocio-encu_diarias').val(sumatot);
            
        });





    });



    
</script>