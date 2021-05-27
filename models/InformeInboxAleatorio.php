<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_informe_inbox_aleatorio".
 *
 * @property integer $id
 * @property string $pcrc
 * @property string $encu_diarias_pcrc
 * @property string $encu_diarias_totales
 * @property string $encu_mes_pcrc
 * @property string $encu_mes_totales
 * @property string $faltaron
 * @property string $disponibles
 * @property string $estado
 * @property string $fecha_creacion
 */
class InformeInboxAleatorio extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_informe_inbox_aleatorio';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['pcrc'], 'required'],
            [['estado'], 'string'],
            [['fecha_creacion'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'pcrc' => Yii::t('app', 'PCRC'),
            'encu_diarias_pcrc' => Yii::t('app', 'Meta diaria'),
            'encu_diarias_totales' => Yii::t('app', 'Cantidad real diaria'),
            'encu_mes_pcrc' => Yii::t('app', 'Meta mes'),
            'encu_mes_totales' => Yii::t('app', 'Cantidad real mes'),
            'faltaron' => Yii::t('app', 'Faltantes'),
            'disponibles' => Yii::t('app', 'Disponibles para completar'),
            'estado' => Yii::t('app', 'Estado'),
            'fecha_creacion' => Yii::t('app', 'Fecha creación'),
        ];
    }

}
