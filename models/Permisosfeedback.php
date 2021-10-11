<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_permisosfeedback".
 *
 * @property integer $idpermisosfeedback
 * @property integer $idusuarios
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class Permisosfeedback extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_permisosfeedback';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idusuarios', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idpermisosfeedback' => Yii::t('app', ''),
            'idusuarios' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}