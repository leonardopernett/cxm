<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_registro_ejec".
 *
 * @property integer $id
 * @property integer $valorador_id
 * @property integer $valorado_id
 * @property integer $pcrc_id
 * @property integer $dimension_id
 * @property integer $ejec_form_id
 * @property string $descripcion
 * @property string $fecha_modificacion
 *
 * @property TblArbols $pcrc
 * @property TblDimensions $dimension
 * @property TblUsuarios $valorador
 * @property TblEvaluados $valorado
 */
class RegistroEjec extends \yii\db\ActiveRecord
{
    public $enviar_form;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_registro_ejec';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ejec_form_id','valorado_id','dimension_id','pcrc_id'], 'required','on'=>'adicionar'],
            [['valorador_id', 'ejec_form_id','valorado_id','pcrc_id', 'descripcion'], 'required','on'=>'escalar'],
            [['valorador_id', 'valorado_id', 'pcrc_id', 'dimension_id', 'ejec_form_id','tipo_interaccion'], 'integer'],
            [['fecha_modificacion','enviar_form'], 'safe'],
            [['descripcion'], 'string', 'max' => 500]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'valorador_id' => Yii::t('app', 'Valorador ID'),
            'valorado_id' => Yii::t('app', 'Evaluado ID'),
            'pcrc_id' => Yii::t('app', 'Arbol'),
            'dimension_id' => Yii::t('app', 'Dimension ID'),
            'ejec_form_id' => Yii::t('app', 'Ejec Form ID'),
            'descripcion' => Yii::t('app', 'Descripcion'),
            'fecha_modificacion' => Yii::t('app', 'Fecha Modificacion'),
            'enviar_form' =>Yii::t('app', 'enviar_form')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcrc()
    {
        return $this->hasOne(TblArbols::className(), ['id' => 'pcrc_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDimension()
    {
        return $this->hasOne(TblDimensions::className(), ['id' => 'dimension_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValorador()
    {
        return $this->hasOne(TblUsuarios::className(), ['usua_id' => 'valorador_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getValorado()
    {
        return $this->hasOne(TblEvaluados::className(), ['id' => 'valorado_id']);
    }
    
    /**
     * Metodo que retorna el listado de dimensiones
     * 
     * @return array
     * @author Daniel Enciso <daniel.enciso@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getDimensionsList() {
        return ArrayHelper::map(Dimensiones::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

}
