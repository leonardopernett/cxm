<?php

use yii\helpers\Html;

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Calificaciondetalles */
/* @var $form yii\widgets\ActiveForm */
?>

<?php

Modal::begin([
    'header' => Yii::t('app', 'Calculos Feedback'),
    'id' => 'modal-calculoFeedback',
    'size' => 'modal-big',
    'clientOptions' => [
        'show' => true,
    ],
]);
?>

<?php

if (!empty($data) && count($data) > 0) {
    $arrIgnoredCols = array();    
    $html = '';
    $thead = '<thead><tr>';
    $tbody = '<tbody>';
    foreach ($data as $i => $row) {
        $tbody.= '<tr>';
        foreach ($row as $cell_name => $cell_val) {
            if ($cell_name == 'id' || $cell_name == 'usua_id'){ 
                continue;                 
            }
            if ($i == 0) {
                if (empty($cell_val)) {
                    $arrIgnoredCols[] = $cell_name;
                    continue;
                }
                $thead.= '<th>' . $cell_val . '</th>';
            } else {
                if (in_array($cell_name, $arrIgnoredCols)){
                    continue;
                }
                $tbody.= '<td>' . $cell_val . '</td>';
            }
        }
        $tbody.= '</tr>';
    }

    $thead.= '</tr></thead>';
    $tbody.= '</tbody>';
    $html = '<table class="table table-striped table-bordered">' . $thead 
            . $tbody . '</table>';
    echo $html;
}
?>

<?php Modal::end(); ?> 


