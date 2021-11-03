<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_dataacademica".
 *
 * @property integer $hv_idacademica
 * @property integer $hv_idpersonal
 * @property integer $idhvcursosacademico
 * @property integer $activo
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaDataacademica extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_dataacademica';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hv_idpersonal', 'idhvcursosacademico', 'activo', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idacademica' => Yii::t('app', ''),
            'hv_idpersonal' => Yii::t('app', ''),
            'idhvcursosacademico' => Yii::t('app', ''),
            'activo' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}