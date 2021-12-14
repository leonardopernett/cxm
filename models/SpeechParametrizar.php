<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "tbl_speech_parametrizar".
 *
 * @property integer $idspeechparametrizar
 * @property integer $id_dp_clientes
 * @property string $cod_pcrc
 * @property string $rn
 * @property string $ext
 * @property string $usuared
 * @property string $comentarios
 * @property string $fechacreacion
 * @property integer $usua_id
 * @property integer $anulado
 */
class SpeechParametrizar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_speech_parametrizar';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_dp_clientes', 'usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['cod_pcrc', 'comentarios'], 'string', 'max' => 100],
            [['rn', 'ext'], 'string', 'max' => 20],
            [['usuared'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idspeechparametrizar' => Yii::t('app', ''),
            'id_dp_clientes' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'rn' => Yii::t('app', ''),
            'ext' => Yii::t('app', ''),
            'usuared' => Yii::t('app', ''),
            'comentarios' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }



    public function ObtenerCategorias($params)
    {              
        $sessiones = Yii::$app->user->identity->id;

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
            $query = SpeechParametrizar::find()
                ->select('id_dp_clientes')->distinct()
                ->where("anulado = 0")
                ->andwhere("fechacreacion > '2020-01-01'");
        }else{
            $query = SpeechParametrizar::find()
                ->select(['tbl_speech_parametrizar.id_dp_clientes'])->distinct()
                ->join('LEFT OUTER JOIN', 'tbl_speech_servicios',
                           'tbl_speech_parametrizar.id_dp_clientes = tbl_speech_servicios.id_dp_clientes')
                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                           'tbl_speech_servicios.arbol_id = tbl_arbols.id')
                ->join('LEFT OUTER JOIN', 'tbl_permisos_grupos_arbols',
                           'tbl_arbols.id = tbl_permisos_grupos_arbols.arbol_id')
                ->join('LEFT OUTER JOIN', 'tbl_grupos_usuarios',
                           'tbl_permisos_grupos_arbols.grupousuario_id = tbl_grupos_usuarios.grupos_id')
                ->join('LEFT OUTER JOIN', 'rel_grupos_usuarios',
                           'tbl_grupos_usuarios.grupos_id = rel_grupos_usuarios.grupo_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                           'rel_grupos_usuarios.usuario_id = tbl_usuarios.usua_id')
                ->where("tbl_speech_parametrizar.anulado = 0")
                ->andwhere("tbl_usuarios.usua_id = $sessiones")
                ->andwhere("tbl_speech_parametrizar.fechacreacion > '2020-01-01'");
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


        $data = Yii::$app->db->createCommand("select distinct nameArbol from tbl_speech_servicios where id_dp_clientes = $varOpcion1")->queryScalar();


        return $data;
    }

    public function getTotalCC($opcion1){
        $varOpcion1 = $opcion1;

        $querys =  new Query;
        $querys    ->select(['tbl_speech_categorias.pcrc'])->distinct()
                ->from('tbl_speech_categorias')
                ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                           'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
                ->join('LEFT OUTER JOIN', 'tbl_speech_servicios',
                           'tbl_speech_parametrizar.id_dp_clientes = tbl_speech_servicios.id_dp_clientes')
               ->where('tbl_speech_servicios.id_dp_clientes = '.$varOpcion1.'')
               ->andwhere("tbl_speech_parametrizar.anulado = 0");                    
        $command = $querys->createCommand();
        $query = $command->queryAll();

        $data = count($query);

        return $data;
    }

    public function getTotalSCategorias($opcion1){
        $varOpcion1 = $opcion1;

        $querys =  new Query;
        $querys    ->select(['count(tbl_speech_categorias.idcategoria)'])
                ->from('tbl_speech_categorias')
                ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                           'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
                ->join('LEFT OUTER JOIN', 'tbl_speech_servicios',
                           'tbl_speech_parametrizar.id_dp_clientes = tbl_speech_servicios.id_dp_clientes')
               ->where('tbl_speech_servicios.id_dp_clientes = '.$varOpcion1.'')
               ->andwhere("tbl_speech_parametrizar.anulado = 0");                    
        $command = $querys->createCommand();
        $query = $command->queryScalar();

        return $query;
    }


    public function ObtenerCategorias2($opcion1,$opcion2){
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
            $query = SpeechParametrizar::find()
                ->select('cod_pcrc')->distinct()
                ->where("anulado = 0")
                ->andwhere("id_dp_clientes = '$varOpcion2'")
                ->andwhere("fechacreacion > '2020-01-01'");
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
             return $dataProvider;
        }

        return $dataProvider; 
    }

    public function getListaCC($opcion){
        $varOpcion1 = $opcion;

        $querys =  new Query;
        $querys ->select(['tbl_speech_categorias.pcrc'])->distinct()
                ->from('tbl_speech_categorias')
                ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                           'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
                ->join('LEFT OUTER JOIN', 'tbl_speech_servicios',
                           'tbl_speech_parametrizar.id_dp_clientes = tbl_speech_servicios.id_dp_clientes')
               ->where("tbl_speech_categorias.cod_pcrc like '$varOpcion1'")
               ->andwhere("tbl_speech_parametrizar.anulado = 0");                    
        $command = $querys->createCommand();
        $query = $command->queryScalar();

        return $query;
    }

    public function getTotalesCC($opcion){
        $varOpcion1 = $opcion;

        $querys =  new Query;
        $querys ->select(['count(tbl_speech_categorias.idcategoria)'])->distinct()
                ->from('tbl_speech_categorias')
                ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                           'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
               ->where("tbl_speech_categorias.cod_pcrc like '$varOpcion1'")
               ->andwhere("tbl_speech_parametrizar.anulado = 0");                    
        $command = $querys->createCommand();
        $query = $command->queryScalar();

        return $query;
    }

}