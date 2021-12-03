<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;

$this->title = Yii::t('app', 'Reporte por Variables');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Reportes'), 'url' => ['variables']];
$this->params['breadcrumbs'][] = $this->title;
?>

<!--<div class="page-header">
    <h3><?php // Yii::t('app', 'Reporte por Variables') ?></h3>
</div>-->
<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>
<div class="equipos-evaluados-form">    

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]); ?>

    <div class="row">
        <div class="col-md-6">

            <?=
            $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($model->getArbolesByRoles(), ['id' => 'arbol_id', 'prompt' => 'Seleccione ...'])
            ?>
        </div>
        <div class="col-md-6">  
            <?=
            $form->field($model, 'created', [
                //'addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-calendar"></i>']],                 
//                'inputTemplate' => '<div class="input-group col-md-12">'
//                . '<span class="input-group-addon">'
//                . '<i class="glyphicon glyphicon-calendar"></i>'
//                . '</span>{input}{error}{hint}</div>',
                'labelOptions' => ['class' => 'col-md-12'],
                'template' => '<div class="col-md-4">{label}</div>'
                . '<div class="col-md-8"><div class="input-group">'
                . '<span class="input-group-addon" id="basic-addon1">'
                . '<i class="glyphicon glyphicon-calendar"></i>'
                . '</span>{input}</div>{error}{hint}</div>',
                'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                'options' => ['class' => 'drp-container form-group']
            ])->widget(DateRangePicker::classname(), [
                'useWithAddon' => true,
                'convertFormat' => true,
                'presetDropdown' => true,
                'readonly' => 'readonly',
                'pluginOptions' => [
                    'timePicker' => false,
                    //'timePickerIncrement' => 15,
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'left'
            ]]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?=
            $form->field($model, 'dimension_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($model->getDimensionsList(), ['prompt' => 'Seleccione ...'])
            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo $form->field($model, 'pregunta_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->widget(DepDrop::classname(), [
                'options' => ['id' => 'pregunta_id'],
                'pluginOptions' => [
                    'depends' => ['arbol_id'],
                    'placeholder' => Yii::t('app', 'Select ...'),
                    'url' => Url::to(['/reportes/preguntas/', 'seleccion' => $model->pregunta_id]),
                    'initialize' => true
                ]
            ]);
            ?>
        </div>
    </div>




    <div class="row">
        <div class="col-sm-6">
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
            <?php //Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default'])      ?>
        </div>  


    </div>
    <?php ActiveForm::end(); ?> 

</div>

<?php if ($showGrid): ?>
    <br><!--<div class="page-header">
        <h3><?= Yii::t('app', 'Resultados') ?></h3>
    </div>-->
    <?php
    //TRAER LAS CALIFICACIONES
    $sql = 'SELECT cd.`id`, cd.`name` ';
    $sql .= 'FROM `tbl_bloquedetalles` bd, `tbl_calificaciondetalles` cd ';
    $sql .= 'WHERE bd.`id` = ' . Yii::$app->session['rptFilterValorados']['Tmpreportes']['pregunta_id'] . ' AND ';
    $sql .= 'bd.`calificacion_id` = cd.`calificacion_id` ';
    $sql .= 'ORDER BY cd.`nmorden` ';
    $command = \Yii::$app->db->createCommand($sql);
    $result = $command->queryAll();
    $col = 16;
    $columnasPreguntas = $valColPre = array();
    foreach ($result as $value) {
        if ($col == 20) {
            break;
        }
        $valColPre['header'] = $value['name'];
        $valColPre['attribute'] = "si";
        $valColPre['value'] = 'col' . $col;
        $columnasPreguntas[] = $valColPre;
        $col++;
    }
    $gridColumns = [
        //['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'Cedula valorado',
            'value' => 'col1'
        ],
        [
            'attribute' => 'Nombre Valorado',
            'value' => 'col2'
        ],
        [
            'attribute' => 'Dimension',
            'value' => 'col3'
        ],
        [
            'attribute' => 'n_valoraciones',
            'value' => 'col4'
        ],
        [
            'attribute' => 'SCORE',
            'value' => 'col5'
        ],
        [
            'attribute' => 'pec',
            'value' => 'col6'
        ],
        [
            'attribute' => 'PENC',
            'value' => 'col7'
        ],
        [
            'attribute' => 'spc',
            'value' => 'col8'
        ],
        [
            'attribute' => 'carino',
            'value' => 'col9'
        ],
        [
            'attribute' => 'na',
            'value' => 'col10'
        ],
        [
            'attribute' => 'no',
            'value' => 'col11'
        ],
        [
            'attribute' => 'texto7',
            'value' => 'col12'
        ],
        [
            'attribute' => 'texto8',
            'value' => 'col13'
        ],
        [
            'attribute' => 'texto9',
            'value' => 'col14'
        ],
        [
            'attribute' => 'texto10',
            'value' => 'col15'
        ],
            //['class' => 'yii\grid\ActionColumn'],
    ];
    foreach ($columnasPreguntas as $value) {
        array_push($gridColumns, $value);
    }    
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
        'filename' => Yii::t('app', 'Reporte_variables') . '_' . date('Ymd'),
        'exportRequestParam' => 'exportvariables',
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
<?php endif; ?>