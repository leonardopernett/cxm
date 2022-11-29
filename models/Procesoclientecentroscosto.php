<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_proceso_cliente_centrocosto".
 *
 * @property integer $idvolumendirector
 * @property string $ciudad
 * @property string $director_programa
 * @property string $documento_director
 * @property string $gerente_cuenta
 * @property string $documento_gerente
 * @property integer $id_dp_clientes
 * @property string $cliente
 * @property integer $id_dp_centros_costos
 * @property string $centros_costos
 * @property string $cod_pcrc
 * @property string $pcrc
 * @property integer $estado
 * @property integer $anulado
 * @property string $fechacreacion
 * @property string $feachamodificacion
 * @property integer $usua_id
 */
class Procesoclientecentroscosto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_proceso_cliente_centrocosto';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_dp_clientes', 'id_dp_centros_costos', 'estado', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion', 'feachamodificacion'], 'safe'],
            [['ciudad'], 'string', 'max' => 80],
            [['director_programa', 'gerente_cuenta', 'cliente'], 'string', 'max' => 100],
            [['documento_director', 'documento_gerente', 'cod_pcrc'], 'string', 'max' => 20],
            [['centros_costos', 'pcrc'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idvolumendirector' => Yii::t('app', ''),
            'ciudad' => Yii::t('app', ''),
            'director_programa' => Yii::t('app', ''),
            'documento_director' => Yii::t('app', ''),
            'gerente_cuenta' => Yii::t('app', ''),
            'documento_gerente' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'cliente' => Yii::t('app', ''),
            'id_dp_centros_costos' => Yii::t('app', ''),
            'centros_costos' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'pcrc' => Yii::t('app', ''),
            'estado' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'feachamodificacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}