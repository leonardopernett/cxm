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
class DirectorioCad extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_directorio_cad';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vicepresidente', 'gerente', 'sociedad', 'ciudad', 'sector', 'cliente', 'tipo_canal', 'otro_canal', 'proveedores', 'nom_plataforma'], 'string'],
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['etapa'], 'string', 'max' => 50],
            [['directorprog', 'tipo'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_directorcad' => Yii::t('app', ''),
            'vicepresidente' => Yii::t('app', ''),
            'gerente' => Yii::t('app', ''),
            'sociedad' => Yii::t('app', ''),
            'ciudad' => Yii::t('app', ''),
            'sector' => Yii::t('app', ''),
            'cliente' => Yii::t('app', ''),
            'tipo_canal' => Yii::t('app', ''),
            'otro_canal' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'proveedores' => Yii::t('app', ''),
            'nom_plataforma' => Yii::t('app', ''),
            'etapa' => Yii::t('app', ''),
            'directorprog' => Yii::t('app', ''),
            'tipo' => Yii::t('app', ''),
        ];
    }
}