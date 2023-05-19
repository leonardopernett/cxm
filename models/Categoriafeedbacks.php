<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_categoriafeedbacks".
 *
 * @property integer $id
 * @property string $name
 *
 * @property TblTipofeedbacks[] $tblTipofeedbacks
 */
class Categoriafeedbacks extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_categoriafeedbacks';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return array(
            array('name', 'required'),
            array('name','filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING);}),
            /*array(
                'name',
                'match', 'not' => true, 'pattern' => '/[^a-zA-Z\s()_-]/',
            ),*/
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipofeedbacks() {
        return $this->hasMany(Tipofeedbacks::className(),
                        ['categoriafeedback_id' => 'id']);
    }
    
    /**
     * Metodo que retorna el listado de categorias
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getCategoriasList(){
        return ArrayHelper::map(Categoriafeedbacks::find()->orderBy('name')->asArray()->all(),'id', 'name');        
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

    // funcion para buscar todas las categorias
    public function searchlist(){
        $query = Categoriafeedbacks::find()
                    ->orderBy([
                              'id' => SORT_DESC
                            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);   

        return  $dataProvider;
    }
}
