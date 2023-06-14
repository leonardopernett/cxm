<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_genesys_parametroasesor".
 *
 * @property integer $id_genesysparamsasesor
 * @property string $id_genesys
 * @property string $nombre_asesor
 * @property string $documento_asesor
 * @property string $username_asesor
 * @property string $selfUri
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class GenesysParametroasesor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_genesys_parametroasesor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['id_genesys', 'selfUri'], 'string', 'max' => 200],
            [['nombre_asesor', 'username_asesor'], 'string', 'max' => 100],
            [['documento_asesor'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_genesysparamsasesor' => Yii::t('app', ''),
            'id_genesys' => Yii::t('app', ''),
            'nombre_asesor' => Yii::t('app', ''),
            'documento_asesor' => Yii::t('app', ''),
            'username_asesor' => Yii::t('app', ''),
            'selfUri' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}