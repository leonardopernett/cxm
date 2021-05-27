<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ControlvocBloque1;
use yii\db\Query;

/**
 * ControlProcesosEquipos represents the model behind the search form about `app\models\ControlvocBloque1`.
 */
class ControlProcesosReportAlinearVOC extends ControlvocBloque1
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['id', 'responsable', 'evaluados_id'], 'integer'],
            // [['evaluados_id', 'salario', 'tipo_corte'], 'safe'],
            [['idbloque1'], 'integer'],
            [['fechacreacion', 'valorador_id', 'arbol_id', 'dimensions', 'lider_id', 'tecnico_id', 'numidextsp', 'usuagente', 'duracion','extencion'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function buscarAlinearVoc($params)
    {
        $sessiones = Yii::$app->user->identity->id;  
        //$sessiones = 2953;
        $varAnulado = 0;
        $varMed = 2;
        $varBog = 98;
        $varK = 1;
        $varDimension = '"Alinear + VOC"';


    $rol =  new Query;
    $rol      ->select(['tbl_roles.role_id'])
              ->from('tbl_roles')
                  ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                           'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                  ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                           'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                  ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();
  
     if ($roles == "270"){ 
        $query = ControlvocBloque1::find()->distinct()
            //$query->select('*');
            ->from('tbl_controlvoc_bloque1 cvb')            
            ->join('LEFT JOIN', 'tbl_arbols a', 'cvb.arbol_id = a.id')
            ->join('LEFT JOIN', 'tbl_usuarios u', 'cvb.valorador_id = u.usua_id')
            ->join('LEFT JOIN', 'tbl_usuarios uu', 'cvb.lider_id = uu.usua_id')
            ->join('LEFT JOIN', 'tbl_evaluados e', 'cvb.tecnico_id = e.id')
            ->join('LEFT JOIN', 'rel_grupos_usuarios rgu','u.usua_id = rgu.usuario_id')
            ->join('LEFT JOIN', 'tbl_grupos_usuarios gu', 'rgu.grupo_id = gu.grupos_id')
            ->join('LEFT JOIN', 'tbl_permisos_grupos_arbols ga', 'gu.grupos_id = ga.grupousuario_id')
            ->Where('a.activo = '.$varAnulado.'')
            ->andWhere('a.arbol_id != '.$varK.'')
            ->andWhere('cvb.dimensions = '.$varDimension.'')
            ->orderBy("cvb.idbloque1 DESC");
     }
     else{

        $query = ControlvocBloque1::find()->distinct()
            //$query->select('*');
            ->from('tbl_controlvoc_bloque1 cvb')            
            ->join('LEFT JOIN', 'tbl_arbols a', 'cvb.arbol_id = a.id')
            ->join('LEFT JOIN', 'tbl_usuarios u', 'cvb.valorador_id = u.usua_id')
            ->join('LEFT JOIN', 'tbl_usuarios uu', 'cvb.lider_id = uu.usua_id')
            ->join('LEFT JOIN', 'tbl_evaluados e', 'cvb.tecnico_id = e.id')
            ->join('LEFT JOIN', 'rel_grupos_usuarios rgu','u.usua_id = rgu.usuario_id')
            ->join('LEFT JOIN', 'tbl_grupos_usuarios gu', 'rgu.grupo_id = gu.grupos_id')
            ->join('LEFT JOIN', 'tbl_permisos_grupos_arbols ga', 'gu.grupos_id = ga.grupousuario_id')
            ->Where('u.usua_id ='.$sessiones.'')
            ->andWhere('a.activo = '.$varAnulado.'')
            ->andWhere('a.arbol_id != '.$varK.'')
            ->andWhere('cvb.dimensions = '.$varDimension.'')
            ->orderBy("cvb.idbloque1 DESC");
        }


          $dataProvider = new ActiveDataProvider([
              'query' => $query,
          ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cvb.idbloque1' => $this->idbloque1,
        ]);
         $txtFecha = explode(" ", $this->fechacreacion);
        $txtcanti = count($txtFecha);

         if ($txtcanti > 1) {
            $txtfechaini = $txtFecha[0];
            $txtfechafin = $txtFecha[2];
          $query->andFilterWhere(['like', 'a.id', $this->arbol_id])
                ->andFilterWhere(['like', 'e.id', $this->tecnico_id])
                ->andFilterWhere(['like', 'u.usua_id', $this->valorador_id])
                ->andFilterWhere(['like', 'uu.usua_id', $this->lider_id])
                ->andFilterWhere(['between', 'cvb.fechacreacion', $txtfechaini, $txtfechafin]);
     }
         else
         {
          $query->andFilterWhere(['like', 'a.id', $this->arbol_id])
                ->andFilterWhere(['like', 'e.id', $this->tecnico_id])
                ->andFilterWhere(['like', 'u.usua_id', $this->valorador_id])
                ->andFilterWhere(['like', 'uu.usua_id', $this->lider_id]);
         }


        return $dataProvider;
    }
    public function buscarParamet($paramet)
    {
        $arrayPara[] = 0;

        if (!($this->load($paramet) && $this->validate())) {
            return $arrayPara;
        }

        $arrayPara[1] = $this->arbol_id;
        $arrayPara[2] = $this->fechacreacion;
        $arrayPara[3] = $this->valorador_id;
        $arrayPara[4] = $this->tecnico_id;
       

        return $arrayPara;
    }

}
