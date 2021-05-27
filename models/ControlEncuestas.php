<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_control_encuestas".
 *
 * @property integer $idcontrolencuestas
 * @property string $nombreencuesta
 * @property string $idlimeencuesta
 * @property string $comentariosenc
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class ControlEncuestas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_control_encuestas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombreencuesta', 'comentariosenc'], 'string', 'max' => 100],
            [['idlimeencuesta'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idcontrolencuestas' => Yii::t('app', ''),
            'nombreencuesta' => Yii::t('app', ''),
            'idlimeencuesta' => Yii::t('app', ''),
            'comentariosenc' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}