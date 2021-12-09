<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_preguntas".
 *
 * @property integer $id
 * @property string $pre_indicador
 * @property integer $categoria
 * @property string $enunciado_pre
 * @property integer $id_parametrizacion
 *
 * @property TblParametrizacionEncuesta $idParametrizacion
 * @property TblCategorias $categoria0
 */
class Preguntas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_preguntas';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_parametrizacion'], 'required'],
            [['categoria', 'id_parametrizacion'], 'integer'],
            [['pre_indicador'], 'string', 'max' => 100],
            [['enunciado_pre'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'pre_indicador' => Yii::t('app', 'Pre Indicador'),
            'categoria' => Yii::t('app', 'Categoria'),
            'enunciado_pre' => Yii::t('app', 'Enunciado Pre'),
            'id_parametrizacion' => Yii::t('app', 'Id Parametrizacion'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdParametrizacion()
    {
        return $this->hasOne(TblParametrizacionEncuesta::className(), ['id' => 'id_parametrizacion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria0()
    {
        return $this->hasOne(TblCategorias::className(), ['id' => 'categoria']);
    }
    
    /**
     * 23/02/2016 -> Funcion que permite llevar un log o registro de los datos modificados
     * @param type $insert
     * @return boolean
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert == false) {
                $modelLog = new Logeventsadmin();
                $modelLog->datos_nuevos = print_r($this->attributes, true);
                $modelLog->datos_ant = print_r($this->oldAttributes, true);
                $modelLog->fecha_modificacion = date("Y-m-d H:i:s");
                $modelLog->usuario_modificacion = Yii::$app->user->identity->username;
                $modelLog->id_usuario_modificacion = Yii::$app->user->identity->id;
                $modelLog->tabla_modificada = $this->tableName();
                $modelLog->save();
            }
            return true;
        } else {
            return false;
        }
    }
}
