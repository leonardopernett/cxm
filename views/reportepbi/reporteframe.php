<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;


$this->title = 'Reporte Power BI';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Reporte Power BI -- Propiedades --';


// $rutaframe = "https://app.powerbi.com/reportEmbed?reportId=c9266a88-857e-4383-8e59-57196822f246&groupId=7cc1ac20-d3ed-4e01-b4bb-252845fc9b85&w=2&config=eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly9XQUJJLVNPVVRILUNFTlRSQUwtVVMtcmVkaXJlY3QuYW5hbHlzaXMud2luZG93cy5uZXQiLCJlbWJlZEZlYXR1cmVzIjp7Im1vZGVybkVtYmVkIjpmYWxzZX19";

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
?>
&nbsp; 
  <?= Html::a('Nuevo Reporte',  ['reporte'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Nuevo Reporte']) 
  ?>
<br>
    <div class="page-header" >
        <h3 style="text-align: center;"><?= Html::encode($this->title) ?></h3>
    </div> 
<br>
<div style="position: relative; height: 0; overflow: hidden; padding-bottom: 56.2%; margin-bottom: 20px; " >
    <iframe title="new-page"  style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src='<?php echo $rutaframe; ?>'
            allowfullscreen="" frameborder="0"></iframe>
</div>
   
