<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_declinaciones_usuarios".
 *
 * @property integer $id
 * @property string $url
 * @property string $fecha
 * @property string $comentario
 * @property integer $usua_id
 * @property integer $declinacion_id
 * @property integer $arbol_id
 * @property integer $dimension_id
 * @property integer $evaluado_id
 *
 * @property TblArbols $arbol
 * @property TblDeclinaciones $declinacion
 * @property TblDimensions $dimension
 * @property TblEvaluados $evaluado
 * @property TblUsuarios $usua
 */
class DeclinacionesUsuarios extends \yii\db\ActiveRecord {

    const ESTADO_ACTIVO = 1;
    const ESTADO_INACTIVO = 0;

    public $formulario_id;
    public $rol;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_declinaciones_usuarios';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['fecha', 'usua_id', 'declinacion_id', 'arbol_id', 'dimension_id',
            'evaluado_id'], 'required'],
            [['fecha'], 'safe'],
            [['usua_id', 'declinacion_id', 'arbol_id', 'dimension_id', 'evaluado_id'],
                'integer'],
            [['url', 'comentario'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'url' => Yii::t('app', 'Url'),
            'fecha' => Yii::t('app', 'Fecha'),
            'comentario' => Yii::t('app', 'Comentario'),
            'usua_id' => Yii::t('app', 'Evaluador'),
            'declinacion_id' => Yii::t('app', 'Declinacion ID'),
            'arbol_id' => Yii::t('app', 'Arbol ID'),
            'dimension_id' => Yii::t('app', 'Dimension ID'),
            'evaluado_id' => Yii::t('app', 'Evaluado ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbol() {
        return $this->hasOne(Arboles::className(), ['id' => 'arbol_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDeclinacion() {
        return $this->hasOne(Declinaciones::className(), ['id' => 'declinacion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDimension() {
        return $this->hasOne(Dimensiones::className(), ['id' => 'dimension_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluado() {
        return $this->hasOne(Evaluados::className(), ['id' => 'evaluado_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'usua_id']);
    }

    /**
     * Obtiene el listado de las declinaciones
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getDeclinacionesActiveList() {        
        return ArrayHelper::map(Declinaciones::find()->where(
                                ['estado' => static::ESTADO_ACTIVO])->orderBy('nombre')->asArray()->all(), 'id', 'nombre');
    }

    /**
     * Obtiene el listado de las declinaciones
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getDeclinacionesList() {
        return ArrayHelper::map(Declinaciones::find()->orderBy('nombre')->asArray()->all(), 'id', 'nombre');
    }

}
