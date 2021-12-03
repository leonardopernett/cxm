<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_distribucion_asesores".
 *
 * @property integer $iddistriasesor
 * @property string $cedulaasesor
 * @property string $cedulalider
 * @property string $fechaactualjarvis
 * @property integer $id_dp_clientes
 * @property string $cod_pcrc
 * @property string $fechamodificacxm
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class DistribucionAsesores extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_distribucion_asesores';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechaactualjarvis', 'fechamodificacxm', 'fechacreacion'], 'safe'],
            [['id_dp_clientes', 'anulado', 'usua_id'], 'integer'],
            [['cedulaasesor', 'cedulalider', 'cod_pcrc'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddistriasesor' => Yii::t('app', ''),
            'cedulaasesor' => Yii::t('app', ''),
            'cedulalider' => Yii::t('app', ''),
            'fechaactualjarvis' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'fechamodificacxm' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}