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
class Casosqyr extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_qr_casos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_solicitud','id_area','id_tipologia','id_estado_caso','anulado','usua_id'], 'integer'],
            [['fecha_creacion'], 'safe'],
            [['comentario'], 'string', 'max' => 500],
            [['nombre','documento','correo','cliente','numero_caso','archivo','archivo2'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', ''),
            'id_solicitud' => Yii::t('app', ''),
            'id_area' => Yii::t('app', ''),
            'id_tipologia' => Yii::t('app', ''),
            'comentario' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),
            'documento' => Yii::t('app', ''),            
            'correo' => Yii::t('app', ''),
            'cliente' => Yii::t('app', ''),
            'numero_caso' => Yii::t('app', ''),
            'archivo' => Yii::t('app', ''),
            'archivo2' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacrecion' => Yii::t('app', ''),
        ];
    }
}
