<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_filtros_control".
 *
 * @property integer $id
 * @property integer $usua_id
 * @property string $ids_arboles
 * @property string $ids_dimensiones
 * @property string $ids_metricas
 * @property string $rango_fecha
 * @property string $ids_roles
 * @property string $ids_valoradores
 * @property string $ids_equipos_valoradores
 * @property integer $guardar_filtro
 *
 * @property TblUsuarios $usua
 */
class FiltrosControl extends \yii\db\ActiveRecord {

    //variables para filtros de busqueda en control proceso y persona
    public $arbol;
    public $fecha;
    public $metrica;
    public $dimension;
    public $corte;
    public $rol;
    public $valorador;
    public $equiposvalorador;
    public $arbolDetallada;
    public $fechaDetallada;
    public $metricaDetallada;
    public $dimensionDetallada;
    public $corteDetallada;
    public $selecGrafica;
    public $rolDetallada;
    public $valoradorDetallada;
    public $equiposvaloradorDetallada;
    public $tipo_grafica;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_filtros_control';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['usua_id', 'guardar_filtro', 'corte_id', 'bandera_correo'], 'integer'],
            [['ids_arboles', 'ids_dimensiones', 'ids_metricas', 'ids_roles', 'ids_valoradores', 'ids_equipos_valoradores', 'rango_fecha'], 'string', 'max' => 255],
            [['fecha', 'metrica', 'dimension', 'corte', 'arbol', 'tipo_grafica'], 'required', 'on' => 'filtroProceso'],
            [['fechaDetallada', 'metricaDetallada', 'dimensionDetallada', 'corteDetallada', 'arbol'], 'required', 'on' => 'filtroProcesoDetallado'],
            [['arbol', 'fecha', 'metrica', 'dimension', 'corte', 'selecGrafica', 'rol', 'valorador',
            'equiposvalorador', 'rolDetallada', 'valoradorDetallada', 'equiposvaloradorDetallada', 'tipo_grafica'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'usua_id' => Yii::t('app', 'Usua ID'),
            'ids_arboles' => Yii::t('app', 'Ids Arboles'),
            'ids_dimensiones' => Yii::t('app', 'Ids Dimensiones'),
            'ids_metricas' => Yii::t('app', 'Ids Metricas'),
            'rango_fecha' => Yii::t('app', 'Rango Fecha'),
            'guardar_filtro' => Yii::t('app', 'Guardar Filtro'),
            'selecGrafica' => Yii::t('app', 'Seleccionar Gráfica'),
            'fechaDetallada' => Yii::t('app', 'Fecha'),
            'metricaDetallada' => Yii::t('app', 'Metrica'),
            'dimensionDetallada' => Yii::t('app', 'Dimension'),
            'metrica' => Yii::t('app', 'Metrica'),
            'dimension' => Yii::t('app', 'Dimension'),
            'corteDetallada' => Yii::t('app', 'Corte'),
            'bandera_correo' => Yii::t('app', 'Enviar Correo'),
            'equiposvalorador' => Yii::t('app', 'equiposvaloradorDetallada'),
            'equiposvaloradorDetallada' => Yii::t('app', 'equiposvaloradorDetallada'),
            'tipo_grafica' => Yii::t('app', 'Tipo de Gráfica'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua() {
        return $this->hasOne(TblUsuarios::className(), ['usua_id' => 'usua_id']);
    }

}
