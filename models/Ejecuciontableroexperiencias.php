<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_ejecuciontableroexperiencias".
 *
 * @property integer $id
 * @property integer $ejecucionformulario_id
 * @property integer $tableroenfoque_id
 * @property integer $tableroproblemadetalle_id
 * @property string $dsenfoque
 * @property string $dsproblema
 * @property string $detalle
 *
 * @property TblEjecucionformularios $ejecucionformulario
 * @property TblTableroenfoques $tableroenfoque
 * @property TblTableroproblemadetalles $tableroproblemadetalle
 */
class Ejecuciontableroexperiencias extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_ejecuciontableroexperiencias';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ejecucionformulario_id', 'tableroenfoque_id', 'tableroproblemadetalle_id'], 'required'],
            [['ejecucionformulario_id', 'tableroenfoque_id', 'tableroproblemadetalle_id'], 'integer'],
            [['detalle'], 'string'],
            [['dsenfoque', 'dsproblema'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'ejecucionformulario_id' => Yii::t('app', 'Ejecucionformulario ID'),
            'tableroenfoque_id' => Yii::t('app', 'Tableroenfoque ID'),
            'tableroproblemadetalle_id' => Yii::t('app', 'Tableroproblemadetalle ID'),
            'dsenfoque' => Yii::t('app', 'Dsenfoque'),
            'dsproblema' => Yii::t('app', 'Dsproblema'),
            'detalle' => Yii::t('app', 'Detalle'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionformulario() {
        return $this->hasOne(Ejecucionformularios::className(), ['id' => 'ejecucionformulario_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTableroenfoque() {
        return $this->hasOne(TblTableroenfoques::className(), ['id' => 'tableroenfoque_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTableroproblemadetalle() {
        return $this->hasOne(Tableroproblemadetalles::className(), ['id' => 'tableroproblemadetalle_id']);
    }

}
