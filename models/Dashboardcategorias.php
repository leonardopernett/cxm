<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_dashboardcategorias".
 *
 * @property integer $iddashcategorias
 * @property integer $idcategoria
 * @property string $nombre
 * @property string $tipocategoria
 * @property string $tipoindicador
 * @property string $clientecategoria
 * @property string $fechacreacion
 * @property integer $anulado
 */
class Dashboardcategorias extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_dashboardcategorias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idcategoria', 'anulado','usua_id', 'usabilidad','iddashservicio'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['nombre', 'tipocategoria', 'tipoindicador', 'clientecategoria','ciudadcategoria','orientacion'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddashcategorias' => Yii::t('app', ''),
            'idcategoria' => Yii::t('app', ''),
            'nombre' => Yii::t('app', ''),
            'tipocategoria' => Yii::t('app', ''),
            'tipoindicador' => Yii::t('app', ''),
            'clientecategoria' => Yii::t('app', ''),
            'ciudadcategoria' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'orientacion' => Yii::t('app', ''),
            'usabilidad' => Yii::t('app', ''),
            'iddashservicio' => Yii::t('app', ''),
        ];
    }

    public function ObtenerCategorias($params)
    {              

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

        if ($roles == '270') {
            $query = Dashboardcategorias::find()
                ->select('clientecategoria')->distinct()
                ->where('anulado = 0');
        }else{
            $query = Dashboardcategorias::find()
                ->select('clientecategoria')->distinct()
                ->where('anulado = 0')
                ->andwhere('usua_id ='.$params.'');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
             return $dataProvider;
        }

        return $dataProvider; 
    }

    public function getArbolPadre($opcion1){
        $varOpcion1 = $opcion1;

        $querys =  new Query;
        $querys     ->select(['tbl_arbols.name'])->distinct()
                    ->from('tbl_arbols')
                    ->join('LEFT OUTER JOIN', 'tbl_dashboardservicios',
                            'tbl_arbols.id = tbl_dashboardservicios.arbol_id')
                    ->where(['like','tbl_dashboardservicios.clientecategoria',$varOpcion1]);
                    
        $command = $querys->createCommand();
        $data = $command->queryScalar(); 

        return $data;
    }

    public function getTotalCategorias($opcion1){
        $varOpcion1 = $opcion1;
        $sessiones = Yii::$app->user->identity->id;
        
        $rol =  new Query;
        $rol    ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                           'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                           'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
               ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
        $command = $rol->createCommand();
        $roles = $command->queryScalar();
        
        if ($roles == '270') {
            $data = Yii::$app->db->createCommand("select count(*) from tbl_dashboardcategorias where clientecategoria like '%$varOpcion1%'")->queryScalar();
        }else{
            $data = Yii::$app->db->createCommand("select count(*) from tbl_dashboardcategorias where clientecategoria like '%$varOpcion1%' and usua_id = '$sessiones'")->queryScalar();
        }

        return $data;
    }

    public function ObtenerCategorias2($param1,$param2)
    {              

        $rol =  new Query;
        $rol    ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                           'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                           'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
               ->where('tbl_usuarios.usua_id = '.$param1.'');                    
        $command = $rol->createCommand();
        $roles = $command->queryScalar();

        if ($roles == '270') {
            $query = Dashboardcategorias::find()
                ->where('anulado = 0')
                ->andwhere(['like','clientecategoria',$param2]);
        }else{
            $query = Dashboardcategorias::find()
                ->where('anulado = 0')
                ->andwhere('usua_id ='.$param1.'')
                ->andwhere(['like','clientecategoria',$param2]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($param2) && $this->validate())) {
             return $dataProvider;
        }

        return $dataProvider; 
    }


}
