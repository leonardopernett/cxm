<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Reporte Segundo Calificador');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Repotes'), 'url' => ['reportesegundocalificador']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Reporte-2-calificador.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Full Page Image Header with Vertically Centered Content -->
<script>
    $(document).ready(function(){
        $.fn.snow();
    });
</script>
<script src="../../js_extensions/mijs.js"> </script>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        
      </div>
    </div>
  </div>
</header>
<br><br>


<div class="equipos-evaluados-form">    
</div>

<?php if ($showGrid): ?>
    <div class="col-sm-12">        
        <?php
        $gridColumns = [
            //['class' => 'yii\grid\SerialColumn'],
            's_fecha',
            'id_caso',
            [
                'header' => 'Programa/PCRC',
                'attribute' => 'id_ejecucion_formulario',
                'value' => function($data) {
                    $ef = app\models\Ejecucionformularios::findOne($data->id_ejecucion_formulario);
                    if (isset($ef)) {
                        $ar = \app\models\Arboles::findOne($ef->arbol_id);
                        return $ar->name;  
                    }else{
                       return '-';  
                    }                   
                }
            ],
            [
                'attribute' => 'id_solicitante',
                'value' => 'idSolicitante.name'
            ],
            [
                'attribute' => 'Cedula Valorado',
                'value' => 'idSolicitante.identificacion'
            ],
            [
                'attribute' => 'id_evaluador',
                'value' => 'idValorador.usua_nombre'
            ],
            [
                'attribute' => 'id_responsable',
                'value' => 'idResponsable.usua_nombre'
            ],
            [
                'attribute' => 'Cedula Responsable',
                'value' => 'idResponsable.usua_identificacion'
            ],
            [
                'attribute' => 'Rol Responsable',
                'value' => 'relUsuariosRoles.roles.role_nombre'
            ],    
            [
                'format' => 'html',
                'attribute' => 'argumento',
                'contentOptions' => [
                'style'=> (!$export) ? 'min-width: 200px; overflow: auto; word-wrap: break-word;' : ''
                ],
                'value' => (!$export) ? function($data) {
                    return substr($data->argumento, 0, 100) . '...';
                } :     function($data) {
                        return $data->argumento;
                }
                /*'contentOptions' => [
                    'style'=>'min-width: 200px; overflow: auto; word-wrap: break-word;'
                ],
                'value' => function($data) {
                    return substr($data['argumento'], 0, 100) . '...';
                }*/
            ],
            [
                'format' => 'html',
                'attribute' => 'estado',
                'value' => function($data) {
                    return $data->getEstados($data->estado_sc);
                }
            ],
        ];

        echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'columnSelectorOptions' => [
                'label' => Yii::t('app', 'Columns'),
            ],
            'dropdownOptions' => [
                'label' => Yii::t('app', 'Export All'),
                'class' => 'btn btn-default'
            ],
            //'fontAwesome' => true,
            'showConfirmAlert' => false,
            'target' => '_blank',
            'filename' => Yii::t('app', 'Reporte_feedback') . '_' . date('Ymd'),
            'exportRequestParam' => 'exportsegundocalificador',
            'columnBatchToggleSettings' => [
                'label' => Yii::t('app', 'All')
            ],
            'exportConfig' => [
                ExportMenu::FORMAT_TEXT => false,
                ExportMenu::FORMAT_PDF => false,
                ExportMenu::FORMAT_HTML => false,
            ]
        ]);
        ?>
        <br/><br/>
        <?php
        echo kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
        ]);
        ?>
    </div>
    <?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
    ?>

<?php endif; ?>