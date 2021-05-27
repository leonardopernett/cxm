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

$this->title = 'DashBoard Voz del Cliente';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
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

    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;

    $fechaI = new DateTime();
    $fechaI->modify('first day of this month');
    $fechaIni = $fechaI->format('Y-'.$MesAnterior.'-d');

    $fechaF = new DateTime();
    $fechaF->modify('last day of this month');
    $fechaFin = $fechaF->format('Y-'.$MesAnterior.'-d');

    $querys =  new Query;
    $querys     ->select(['tbl_arbols.id as ArbolID','tbl_arbols.name as ArbolName'])->distinct()
                ->from('tbl_control_volumenxcliente')
                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_control_volumenxcliente.idservicio = tbl_arbols.id');                    
    $command = $querys->createCommand();
    $query = $command->queryAll();
    $listData = ArrayHelper::map($query, 'ArbolID', 'ArbolName');

    $querys2 =  new Query;
    $querys2     ->select(['tbl_dashboardservicios.iddashboardservicios as IdServicio','tbl_dashboardservicios.nombreservicio as NameServicio'])->distinct()
                ->from('tbl_dashboardservicios')
                ->where(['tbl_dashboardservicios.arbol_id' => null]);                    
    $command2 = $querys2->createCommand();
    $query2 = $command2->queryAll();
    $listData2 = ArrayHelper::map($query2, 'IdServicio', 'NameServicio');

?>
<div class="formularios-form" style="display: inline" id="dtbloque1">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?php  echo $form->field($model, 'arbol_id')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'TipoArbol'])->label('PCRC') ?> 
        <br>

        <?php echo $form->field($model, 'idservicios')->dropDownList($listData2, ['prompt' => 'Seleccionar...', 'id'=>'TipoArbol2'])->label('Servicio Speech') ?> 

        <?= Html::submitButton(Yii::t('app', 'Asignar Pcrc'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'title' => 'Asignar Pcrc']) 
                ?>          

    <?php $form->end() ?>
</div>

