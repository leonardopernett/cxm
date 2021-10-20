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

    

?>
<div class="capaUno" style="display: inline;">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;">Ingresar categoria </label>
                <label for="txtmedio" style="font-size: 14px;"> Tipo Cargue de Base de Medallia</label>
                <select id="txtmedio" class ='form-control'  onchange="accion()">
                          <option value="" disabled selected>seleccione...</option>
                          <option value="2922">Tigo Colombia</option>
                          <option value="3272">Tigo Bolivia</option>
                </select>
                <?= $form->field($model, 'pcrc')->textInput(['maxlength' => 250, 'class'=>'hidden', 'id'=>'Idpcrc', 'placeholder' => 'Ingresar pcrc']) ?>
                <?= $form->field($model, 'nombrecategoria')->textInput(['maxlength' => 250,  'id'=>'IdCategoria', 'placeholder' => 'Ingresar categoria']) ?> 
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
                        <th scope="col" colspan="3" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Listado de categorias registradas') ?></label></th>
                        </tr>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'id') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Categoria') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PCRC') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $varlistarta = Yii::$app->db->createCommand("select * from tbl_basechat_categorias bk where bk.anulado = 0 ")->queryAll();
				$vartipo = '';
                            foreach ($varlistarta as $key => $value) {
                                if($value['pcrc'] == 2922){
                                    $vartipo = 'Tigo Colombia';
                                } else {
                                    $vartipo = 'Tigo Bolivia';
                                }
                        ?>
                        <tr>
                            <td><label style="font-size: 12px;"><?php echo  $value['idlista']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['nombrecategoria']; ?></label></td>
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
<script type="text/javascript">
function accion(){
        var varIdpcrc = document.getElementById("txtmedio").value;
         document.getElementById("Idpcrc").value = varIdpcrc;        
        
    };
</script>