<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_heroes_generalpostulacion".
 *
 * @property integer $id_generalpostulacion
 * @property integer $id_tipopostulacion
 * @property integer $id_postulador
 * @property integer $id_cargospostulacion
 * @property integer $id_postulante
 * @property integer $tipo_postulante
 * @property integer $id_dp_clientes
 * @property string $cod_pcrc
 * @property integer $id_ciudadpostulacion
 * @property string $fecha_interaccion
 * @property string $ext_interaccion
 * @property string $usuario_interaccion
 * @property string $historia_interaccion
 * @property string $idea_postulacion
 * @property integer $estado
 * @property integer $procesos
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class HeroesGeneralpostulacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_heroes_generalpostulacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_tipopostulacion', 'id_postulador', 'id_cargospostulacion', 'id_postulante', 'tipo_postulante', 'id_dp_clientes', 'id_ciudadpostulacion', 'estado', 'procesos', 'anulado', 'usua_id'], 'integer'],
            [['fecha_interaccion', 'fechacreacion'], 'safe'],
            [['historia_interaccion', 'idea_postulacion'], 'string'],
            [['cod_pcrc', 'ext_interaccion', 'usuario_interaccion'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_generalpostulacion' => Yii::t('app', 'I'),
            'id_tipopostulacion' => Yii::t('app', 'I'),
            'id_postulador' => Yii::t('app', 'I'),
            'id_cargospostulacion' => Yii::t('app', ''),
            'id_postulante' => Yii::t('app', ''),
            'tipo_postulante' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'id_ciudadpostulacion' => Yii::t('app', ''),
            'fecha_interaccion' => Yii::t('app', ''),
            'ext_interaccion' => Yii::t('app', ''),
            'usuario_interaccion' => Yii::t('app', ''),
            'historia_interaccion' => Yii::t('app', ''),
            'idea_postulacion' => Yii::t('app', ''),
            'estado' => Yii::t('app', ''),
            'procesos' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}