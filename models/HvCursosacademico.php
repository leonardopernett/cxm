<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hv_cursosacademico".
 *
 * @property integer $idhvcursosacademico
 * @property integer $idhvacademico
 * @property string $hv_cursos
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 *
 * @property TblHvNivelacademico $idhvacademico0
 * @property TblUsuarios $usua
 */
class HvCursosacademico extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hv_cursosacademico';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idhvacademico', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['hv_cursos'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idhvcursosacademico' => Yii::t('app', ''),
            'idhvacademico' => Yii::t('app', ''),
            'hv_cursos' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdhvacademico0()
    {
        return $this->hasOne(TblHvNivelacademico::className(), ['idhvacademico' => 'idhvacademico']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua()
    {
        return $this->hasOne(TblUsuarios::className(), ['usua_id' => 'usua_id']);
    }
}