<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\base\Exception;

/**
 * This is the model class for table "tbl_logeventsadmin".
 *
 * @property integer $id_log
 * @property string $tabla_modificada
 * @property string $datos_ant
 * @property string $datos_nuevos
 * @property string $fecha_modificacion
 * @property string $usuario_modificacion
 * @property integer $id_usuario_modificacion
 */
class Logeventsadmin extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_logeventsadmin';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['fecha_modificacion'], 'safe'],
            [['id_usuario_modificacion'], 'integer'],
            [['tabla_modificada', 'datos_ant', 'datos_nuevos', 'usuario_modificacion'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id_log' => Yii::t('app', 'Id Log'),
            'tabla_modificada' => Yii::t('app', 'Tabla Modificada'),
            'datos_ant' => Yii::t('app', 'Datos Ant'),
            'datos_nuevos' => Yii::t('app', 'Datos Nuevos'),
            'fecha_modificacion' => Yii::t('app', 'Fecha Modificacion'),
            'usuario_modificacion' => Yii::t('app', 'Usuario Modificacion'),
            'id_usuario_modificacion' => Yii::t('app', 'Id Usuario Modificacion'),
        ];
    }

    /**
     * Funcion que genera el excel con los datos seleccionados en los filtros que tiene la vista
     *  index de logeventsadmin
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $models
     * @return boolean
     */
    public function generarReporteLogAdmin($models = null) {
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
            $self = Url::to(['logeventsadmin/index']);
            header("refresh:1; url='$self'");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Reporte_logAdmin.xlsx"');
            header('Cache-Control: max-age=0');

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
