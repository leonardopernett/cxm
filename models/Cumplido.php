<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_qr_casos".
 *
 * @property integer $id 
 * @property integer $id_solicitud 
 * @property integer $id_area 
 * @property integer $id_tipologia 
 * @property string $comentario
 * @property string $documento
 * @property string $nombre
 * @property string $correo
 * @property string $cliente
 * @property string $numero_caso
 * @property string $archivo
 * @property string $archivo2
 * @property integer $id_estado_caso
 * @property string $fecha_creacion
 * @property integer $anulado
 * @property integer $usua_id
 */
class Cumplido extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_qr_cumplido';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_cumplido', 'anulado','usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombre'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_cumplido' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}
