<?php

namespace app\models;
use yii\data\ActiveDataProvider;

use Yii;

/**
 * This is the model class for table "tbl_seguimiento_rendimiento".
 *
 * @property integer $idsr
 * @property integer $evaluados_id
 * @property string $responsable
 * @property integer $idtiposcortes
 * @property string $justificacion
 * @property string $correo
 * @property string $fechacreacion
 */
class Seguimientorendimiento extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_seguimiento_rendimiento';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['evaluados_id', 'idtiposcortes'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['responsable'], 'string', 'max' => 150],
            [['justificacion', 'correo'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idsr' => Yii::t('app', 'Idsr'),
            'evaluados_id' => Yii::t('app', ''),
            'responsable' => Yii::t('app', ''),
            'idtiposcortes' => Yii::t('app', ''),
            'justificacion' => Yii::t('app', ''),
            'correo' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }

}
