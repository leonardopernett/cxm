<?php

namespace app\models;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_detalleparametrizacion".
 *
 * @property integer $id
 * @property string $name_parametrizacion
 * @property integer $categoria
 * @property integer $addNA
 * @property string $configuracion
 * @property integer $id_categoriagestion
 *
 * @property TblCategoriagestion $idCategoriagestion
 * @property TblCategorias $categoria0
 */
class Detalleparametrizacion extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_detalleparametrizacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['categoria','configuracion'], 'required'],
            [['categoria', 'id_categoriagestion', 'addNA'], 'integer'],
            [['name_parametrizacion'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name_parametrizacion' => Yii::t('app', 'Name Parametrizacion'),
            'categoria' => Yii::t('app', 'Categoria'),
            'configuracion' => Yii::t('app', 'Configuración'),
            'id_categoriagestion' => Yii::t('app', 'Id Categoriagestion'),
            'addNA' => Yii::t('app', 'Adicionar "NO APLICA"'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdCategoriagestion()
    {
        return $this->hasOne(Categoriagestion::className(), ['id' => 'id_categoriagestion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria0()
    {
        return $this->hasOne(Categorias::className(), ['id' => 'categoria']);
    }
    
    /**
     * Metodo que retorna el listado de categorias
     * 
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getCategorias(){
        return ArrayHelper::map(Categorias::find()
                                ->all(), 'id', 'nombre');
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
