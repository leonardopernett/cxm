<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_bitacora_universo".
 *
 * 'id_bitacora_uni' => Yii::t('app', ''),
 * 
 * @property integer $id_cliente
 * @property integer $pcrc
 * @property string $ciudad 
 * @property string $director
 * @property string $gerente
 * @property string $medio_contacto
 * @property string $cedula
 * @property string $nombre 
 * @property string $telefono_movil
 * @property string $fecha_registro
 * @property string $grupo
 * @property string $nivel_caso
 * @property integer $id_momento
 * @property integer $id_detalle_momento 
 * @property string $nombre_tutor
 * @property string $nombre_lider
 * @property string $descripcion_caso
 * @property string $escalamiento
 * @property string $responsable 
 * @property string $fecha_escalamiento
 * @property string $fecha_cierre
 * @property string $respuesta
 * @property string $estado
 * @property integer $usua_id 
 * @property string $fecha_creacion
 * @property integer $anulado
 * v
 *
 */
class Controlbitacorauniv extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_bitacora_universo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacreacion','fecha_escalamiento','fecha_cierre','fecha_registro'], 'safe'],
            [['anulado','pcrc','id_momento','id_detalle_momento','usua_id'], 'integer'],
            [['ciudad','director','gerente','medio_contacto','cedula','nombre','telefono_movil','grupo','nivel_caso','nombre_tutor',
            'descripcion_caso','escalamiento','responsable','respuesta','estado'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [            
            'id_bitacora_uni' => Yii::t('app', ''),
            'id_cliente' => Yii::t('app', ''),
            'pcrc' => Yii::t('app', ''),
            'ciudad' => Yii::t('app', ''),
            'director' => Yii::t('app', ''),
            'gerente' => Yii::t('app', ''),
            'medio_contacto' => Yii::t('app', ''),
            'cedula' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),
            'telefono_movil' => Yii::t('app', ''),
            'fecha_registro' => Yii::t('app', ''),
            'grupo' => Yii::t('app', ''),
            'nivel_caso' => Yii::t('app', ''),
            'id_momento' => Yii::t('app', ''),
            'id_detalle_momento' => Yii::t('app', ''),
            'nombre_tutor' => Yii::t('app', ''),
            'nombre_lider' => Yii::t('app', ''),
            'descripcion_caso' => Yii::t('app', ''),
            'escalamiento' => Yii::t('app', ''),
            'responsable' => Yii::t('app', ''),
            'fecha_escalamiento' => Yii::t('app', ''),
            'fecha_cierre' => Yii::t('app', ''),
            'respuesta' => Yii::t('app', ''),
            'estado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fecha_creacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }
    
    public function getmomento($opcion){
        $data = Yii::$app->db->createCommand("Select m.nombre_momento FROM tbl_bitacora_universo bu 
                                            INNER JOIN tbl_momento_bit_uni m ON bu.id_momento = m.id_momento 
                                            WHERE bu.id_bitacora_uni = $opcion")->queryScalar();

       return $data;

    }
    public function getmotivo($opcion){
        $data = Yii::$app->db->createCommand("Select m.detalle_momento FROM tbl_bitacora_universo bu 
                                            INNER JOIN tbl_detalle_momento_bit_uni m ON bu.id_detalle_momento = m.id_detalle_momento 
                                            WHERE bu.id_bitacora_uni = $opcion")->queryScalar();

       return $data;

    }
    public function getcliente($opcion){
        $data = Yii::$app->db->createCommand("select cc.cliente FROM tbl_bitacora_universo bu 
                                            INNER JOIN tbl_proceso_cliente_centrocosto cc ON bu.pcrc = cc.cod_pcrc 
                                             WHERE bu.id_bitacora_uni = $opcion")->queryScalar();

       return $data;

    }
    public function getpcrc($opcion){
        $data = Yii::$app->db->createCommand("select CONCAT(cc.cod_pcrc, ' - ',cc.pcrc)  FROM tbl_bitacora_universo bu 
                                            INNER JOIN tbl_proceso_cliente_centrocosto cc ON bu.pcrc = cc.cod_pcrc 
                                            WHERE bu.id_bitacora_uni = $opcion")->queryScalar();

       return $data;

    }
}