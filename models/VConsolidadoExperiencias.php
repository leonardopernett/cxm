<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "v_consolidado_experiencias".
 *
 * @property string $fecha
 * @property integer $idCliente
 * @property string $cliente
 * @property integer $idPrograma
 * @property string $nombre
 * @property integer $centro_costos
 * @property integer $idObjeto
 * @property string $objeto
 * @property string $cola
 * @property string $ga
 * @property integer $contestadas
 * @property integer $salida
 */
class VConsolidadoExperiencias extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'v_consolidado_experiencias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fecha', 'idCliente', 'cliente', 'idPrograma', 'nombre', 'centro_costos', 'idObjeto', 'objeto', 'cola', 'ga'], 'required'],
            [['fecha'], 'safe'],
            [['idCliente', 'idPrograma', 'centro_costos', 'idObjeto', 'contestadas', 'salida'], 'integer'],
            [['cliente', 'objeto', 'ga'], 'string', 'max' => 50],
            [['nombre'], 'string', 'max' => 100],
            [['cola'], 'string', 'max' => 60]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fecha' => Yii::t('app', 'fecha del indicador. dd/mm/yyyy'),
            'idCliente' => Yii::t('app', 'Id del cliente.
Equivale a los 3 primeros dígitos del centro de costos.'),
            'cliente' => Yii::t('app', 'Nombre o descripción del cliente.'),
            'idPrograma' => Yii::t('app', 'programa del reporte de servicio. Hereda de Programas.'),
            'nombre' => Yii::t('app', 'Nombre del programa'),
            'centro_costos' => Yii::t('app', 'Centro de costos id objeto'),
            'idObjeto' => Yii::t('app', 'id de la cola,grupo,skill. Hereda de objetos_consolidados'),
            'objeto' => Yii::t('app', 'Nombre del objeto que reune la cola y el grupo.'),
            'cola' => Yii::t('app', 'Nombre de la cola'),
            'ga' => Yii::t('app', 'Nombre del grupo de agentes'),
            'contestadas' => Yii::t('app', 'Llamadas contestadas'),
            'salida' => Yii::t('app', 'Llamadas de salida'),
        ];
    }
}