<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\base\Exception;

/**
 * This is the model class for table "tbl_errores_satu".
 *
 * @property integer $id
 * @property string $created
 * @property string $fecha_satu
 * @property string $datos
 * @property string $error
 */
class ErroresSatu extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_errores_satu';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['created', 'fecha_satu', 'datos', 'error'], 'required'],
            [['created', 'fecha_satu'], 'safe'],
            [['datos', 'error'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'created' => Yii::t('app', 'Created'),
            'fecha_satu' => Yii::t('app', 'Fecha Satu'),
            'datos' => Yii::t('app', 'Datos'),
            'error' => Yii::t('app', 'Error'),
        ];
    }

    /**
     * Funcion que genera el excel con los datos seleccionados en los filtros que tiene la vista
     *  index de erroressatu
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $models
     * @return boolean
     */
    public function generarReporteErroressatu($models = null) {
        set_time_limit(0);
        $objPHPexcel = new \PHPExcel();
        $objPHPexcel->setActiveSheetIndex(0);
        $titulos = $this->attributeLabels();
        $column = 'A';
        $row = 2;
        try {
            foreach ($titulos as $titulo) {
                $objPHPexcel->getActiveSheet()->setCellValue($column . '1', $titulo);
                $column++;
            }

            for ($index = 0; $index < count($models); $index++) {
                $column = 'A';
                $model = $models[$index]->getAttributes();
                foreach ($model as $value) {
                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $value);
                    $column++;
                }
                $row++;
            }
            $self = Url::to(['erroressatu/index']);
            header("refresh:1; url='$self'");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Reporte_Erroressatu.xlsx"');
            header('Cache-Control: max-age=1');

            $objWriter = new \PHPExcel_Writer_Excel2007();
            $objWriter->setPHPExcel($objPHPexcel);
            $objWriter->save('php://output');
            Yii::$app->session->setFlash('success', Yii::t('app', 'Reporte generado exitosamente'));
            return true;
        } catch (Exception $exc) {
            return false;
        }
        return false;
    }

}
