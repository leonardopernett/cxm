<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;
use yii\helpers\ArrayHelper;

$txtListResponsable = Yii::$app->db->createCommand("select u.usua_id, u.usua_nombre from tbl_usuarios u inner join rel_usuarios_roles ur on u.usua_id = ur.rel_usua_id inner join tbl_roles r on ur.rel_role_id = r.role_id where 		r.role_id in (270, 274, 276) order by u.usua_nombre")->queryAll();

$listData2 = ArrayHelper::map($txtListResponsable, 'usua_id', 'usua_nombre');

?>
<div id="capaUno" style="display: inline">   
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">            	
            	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            		<div class="row">
                    	<div class="col-sm-12">
                    	<?= $form->field($model, 'usuaidpermiso')->dropDownList($listData2, ['prompt' => 'Seleccionar usuario...', 'id'=>"selectid"])->label('Seleccionar Usuario') ?> 
	                    </div>
	                    <div class="col-sm-12">
                        <?=  $form->field($model, 'arbol_id')->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'arbol_id', 'nameArbol'),
                                        [
                                            'prompt'=>'Seleccione el cliente...'
                                        ]
                            )->label('Seleccionar Servicio:'); 
                        ?>
	                    </div>
	                    <div class="col-sm-12">
	                    	<?= Html::submitButton('Guardar', ['class' => 'btn btn-primary'] ) ?>
	                    </div>
	                </div>
            	<?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>