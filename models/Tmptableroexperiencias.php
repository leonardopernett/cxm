<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tmptableroexperiencias".
 *
 * @property integer $id
 * @property integer $tmpejecucionformulario_id
 * @property integer $tableroenfoque_id
 * @property integer $tableroproblemadetalle_id
 * @property string $detalle
 *
 * @property TblTableroenfoques $tableroenfoque
 * @property TblTableroproblemadetalles $tableroproblemadetalle
 * @property TblTmpejecucionformularios $tmpejecucionformulario
 */
class Tmptableroexperiencias extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tmptableroexperiencias';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tmpejecucionformulario_id', 'tableroenfoque_id', 'tableroproblemadetalle_id'], 'required'],
            [['tmpejecucionformulario_id', 'tableroenfoque_id', 'tableroproblemadetalle_id'], 'integer'],
            [['detalle'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'tmpejecucionformulario_id' => Yii::t('app', 'Tmpejecucionformulario ID'),
            'tableroenfoque_id' => Yii::t('app', 'Tableroenfoque ID'),
            'tableroproblemadetalle_id' => Yii::t('app', 'Tableroproblemadetalle ID'),
            'detalle' => Yii::t('app', 'Detalle'),
        ];
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionformulario() {
        return $this->hasOne(Tmpejecucionformularios::className(), ['id' => 'tmpejecucionformulario_id']);
    }

}
