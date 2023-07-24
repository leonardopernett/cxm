<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_directorio_cad".
 *
 * @property integer $id_directorcad
 * @property string $vicepresidente
 * @property string $gerente
 * @property string $sociedad
 * @property string $ciudad
 * @property string $sector
 * @property string $cliente
 * @property string $tipo_canal
 * @property string $otro_canal
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 * @property string $proveedores
 * @property string $nom_plataforma
 * @property string $etapa
 * @property string $directorprog
 * @property string $tipo
 */
class EtapamultipleCad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_etapamultiple_cad';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_etapamultiplecad','id_etapacad','anulado', 'usua_id','id_directorcad',], 'integer'],
            [['fechacreacion'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_directorcad' => Yii::t('app', ''),
            'id_etapacad' => Yii::t('app', ''),
            'id_etapamultiplecad' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}