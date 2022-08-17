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

$this->title = 'Gestor de Clientes - Permisos';
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
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >

<?php $form = ActiveForm::begin([
    'options' => ["id" => "buscarMasivos"],
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
    ]); ?>
	<div class="capaPrincipal" style="display: inline;">
		<div class="row">
			<div class="col-md-12">
				<div class="card1 mb">
					<label><em class="fas fa-cogs" style="font-size: 20px; color: #2CA5FF;"></em> Seleccionar Permisos Servicios & Pcrc: </label>
					<?=  $form->field($model2, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
	                                          [
	                                              'prompt'=>'Seleccionar...',
	                                              'multiple' => true,
	                                              'size'=>"6",
	                                          ]
	                                                      )->label(''); 
	                ?>
	                <br>
	                <?= Html::submitButton('Agregar Servicios', ['class' => $model2->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>'btn_submit', 'onclick'=>'cargarpage();'] ) ?>
				</div>
			</div>
		</div>
	</div>
<?php $form->end() ?> 
