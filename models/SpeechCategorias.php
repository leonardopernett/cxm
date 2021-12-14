<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_speech_categorias".
 *
 * @property integer $idspeechcategoria
 * @property string $pcrc
 * @property string $cod_pcrc
 * @property string $rn
 * @property string $extension
 * @property string $usua_usuario
 * @property integer $idcategoria
 * @property string $nombre
 * @property string $tipocategoria
 * @property string $tipoindicador
 * @property string $clientecategoria
 * @property integer $orientacionsmart
 * @property integer $tipoparametro
 * @property integer $orientacionform
 * @property integer $usua_id
 * @property integer $usabilidad
 * @property integer $idcategorias
 * @property integer $idciudad
 * @property string $fechacreacion
 * @property integer $anulado
 */
class SpeechCategorias extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_speech_categorias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idcategoria', 'orientacionsmart', 'tipoparametro', 'orientacionform', 'usua_id', 'usabilidad', 'idcategorias', 'idciudad', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['pcrc', 'cod_pcrc', 'rn', 'extension', 'usua_usuario'], 'string', 'max' => 200],
            [['nombre', 'tipocategoria', 'tipoindicador', 'clientecategoria','programacategoria'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idspeechcategoria' => Yii::t('app', ''),
            'pcrc' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'rn' => Yii::t('app', 'Rn'),
            'extension' => Yii::t('app', ''),
            'usua_usuario' => Yii::t('app', ''),
            'idcategoria' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),
            'tipocategoria' => Yii::t('app', ''),
            'tipoindicador' => Yii::t('app', ''),
            'clientecategoria' => Yii::t('app', ''),
            'orientacionsmart' => Yii::t('app', ''),
            'tipoparametro' => Yii::t('app', ''),
            'orientacionform' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'usabilidad' => Yii::t('app', ''),
            'idcategorias' => Yii::t('app', ''),
            'idciudad' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }

        public function ObtenerXCategorias3($opcion1,$opcion2){
        $params = $opcion1;
        $varOpcion2 = $opcion2;

        $rol =  new Query;
        $rol    ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                           'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                           'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
               ->where('tbl_usuarios.usua_id = '.$params.'');                    
        $command = $rol->createCommand();
        $roles = $command->queryScalar();

        // if ($roles == '270') {
            $query = SpeechCategorias::find()
                ->where("anulado = 0")
                ->andwhere("cod_pcrc like '$varOpcion2'")
                ->andwhere("fechacreacion > '2020-01-01'");
      

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
             return $dataProvider;
        }

        return $dataProvider;
    }

}