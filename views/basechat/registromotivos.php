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

$this->title = 'Gestión Satisfacción Chat';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-12">'
    . ' {input}{error}{hint}</div>';
    $sesiones =Yii::$app->user->identity->id;

    $varidlist = Yii::$app->db->createCommand("select idlista, concat(nombrecategoria, ' - ', if(pcrc=3272, 'Tigo Bolivia', 'Tigo Colombia')) as nombre  from tbl_basechat_categorias where anulado = 0")->queryAll();
    $listData = ArrayHelper::map($varidlist, 'idlista', 'nombre');

?>
<div class="capaUno" style="display: inline;">
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
    ]
    ]); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;">Seleccionar categoria </label>
                <?= $form->field($model, "idlista")->dropDownList($listData, ['prompt' => 'Seleccionar categoria...', 'id'=>"idlistas"]) ?>
                <label style="font-size: 15px;">Ingresar motivo </label>
                <?= $form->field($model, 'nombrelista')->textInput(['maxlength' => 250,  'id'=>'Idnombrelista', 'placeholder' => 'Ingresar motivo']) ?> 
                <?= Html::submitButton(Yii::t('app', 'Guardar'),
                                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                'data-toggle' => 'tooltip',
                                                'style' => 'background-color: #4298B4',
                                                'title' => 'Guardar']) 
                ?>  
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>
<hr>
<div class="capaDos" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                <caption>Categorias</caption>
                    <thead>
                        <tr>
                        <th scope="col" colspan="4" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Listado de categorias registradas') ?></label></th>
                        </tr>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'id') ?></label></th>

                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Categoria') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Motivos') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PCRC') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $varlistarta = Yii::$app->db->createCommand("select * from tbl_basechat_motivos bk where bk.anulado = 0 ")->queryAll();
			    $vartipo = '';
                            foreach ($varlistarta as $key => $value) {
                                $varidlista = $value['idlista'];
                                $varcategorias = Yii::$app->db->createCommand("select nombrecategoria from tbl_basechat_categorias bk where bk.anulado = 0 and bk.idlista = $varidlista")->queryScalar();
                                $varpcrc = Yii::$app->db->createCommand("select  pcrc from tbl_basechat_categorias bk where bk.anulado = 0 and bk.idlista = $varidlista")->queryScalar();
				 if($varpcrc == 2922){
                                    $vartipo = 'Tigo Colombia';
                                } else {
                                    $vartipo = 'Tigo Bolivia';
                                }
                        ?>
                        <tr>
                            <td><label style="font-size: 12px;"><?php echo  $value['idbaselista']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varcategorias; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['nombrelista']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $vartipo; ?></label></td>
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
