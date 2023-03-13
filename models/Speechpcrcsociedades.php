<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_speech_pcrcsociedades".
 *
 * @property integer $id_pcrcsociedades
 * @property integer $id_sociedad
 * @property integer $id_dp_clientes
 * @property string $cod_pcrc
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class Speechpcrcsociedades extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_speech_pcrcsociedades';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_sociedad', 'id_dp_clientes', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['cod_pcrc'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_pcrcsociedades' => Yii::t('app', ''),
            'id_sociedad' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}