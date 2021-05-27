<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tmptiposllamada".
 *
 * @property integer $id
 * @property integer $tiposllamadasdetalle_id
 * @property integer $tmpejecucionformulario_id
 *
 * @property TblTmpejecucionformularios $tmpejecucionformulario
 * @property TblTiposllamadasdetalles $tiposllamadasdetalle
 */
class Tmptiposllamada extends \yii\db\ActiveRecord {
    
    public $tiposllamadas;
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tmptiposllamada';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tiposllamadasdetalle_id', 'tmpejecucionformulario_id'], 'required'],
            [['tiposllamadasdetalle_id', 'tmpejecucionformulario_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'tiposllamadasdetalle_id' => Yii::t('app', 'Tiposllamadasdetalle ID'),
            'tmpejecucionformulario_id' => Yii::t('app', 'Tmpejecucionformulario ID'),
            'tiposllamadas' => Yii::t('app', 'Tiposllamadas'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionformulario() {
        return $this->hasOne(Tmpejecucionformularios::className(), ['id' => 'tmpejecucionformulario_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTiposllamadasdetalle() {
        return $this->hasOne(Tiposllamadasdetalles::className(), ['id' => 'tiposllamadasdetalle_id']);
    }

}
