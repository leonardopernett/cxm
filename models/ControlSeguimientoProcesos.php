<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ControlProcesos;
use yii\db\Query;

/**
 * ControlProcesosEquipos represents the model behind the search form about `app\models\Seguimientoprocesos`.
 */
class ControlSeguimientoProcesos extends Seguimientoprocesos
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['evaluados_id'], 'integer'],
            [['cant_valor', 'tipo_corte', 'responsable'], 'safe'],
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
    public function searchseguimiento($params)
    {
	$sesiones =Yii::$app->user->identity->id;

            $varMes = date("n");
            $txtMes = null;
            switch ($varMes) {
                case '1':
                    $txtMes = "Enero";
                    break;
                case '2':
                    $txtMes = "Febrero";
                    break;
                case '3':
                    $txtMes = "Marzo";
                    break;
                case '4':
                    $txtMes = "Abril";
                    break;
                case '5':
                    $txtMes = "Mayo";
                    break;
                case '6':
                    $txtMes = "Junio";
                    break;
                case '7':
                    $txtMes = "Julio";
                    break;
                case '8':
                    $txtMes = "Agosto";
                    break;
                case '9':
                    $txtMes = "Septiembre";
                    break;
                case '10':
                    $txtMes = "Octubre";
                    break;
                case '11':
                    $txtMes = "Noviembre";
                    break;
                case '12':
                    $txtMes = "Diciembre";
                    break;
                default:
                    # code...
                    break;
            }
//var_dump($txtMes); 


        $rol =  new Query;
        $rol     ->select(['tbl_roles.role_id'])
                    ->from('tbl_roles')
                    ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                    ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
        $command = $rol->createCommand();
        $roles = $command->queryScalar();


        if ($roles == "270") {

            $txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0 and tipo_corte like "%'.$txtMes.'%"')->queryScalar(); 
      
                    
            $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();
            $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();  


            $query = ControlProcesos::find()->distinct()                   
                    ->joinWith('ejecucionformularios')
                    ->joinWith('usuarios')
                    ->where(['tbl_control_procesos.anulado' => 'null'])
		    //->andwhere(['like','tbl_control_procesos.tipo_corte',$txtcorte])
                    ->andwhere(['between','tbl_control_procesos.fechacreacion', $fechainiC, $fechafinC]);
        }
        else
        {
            if ($roles == "276" || $roles == "274") {
                if ($sesiones == "70") {
                    $txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0 and tipo_corte like "%'.$txtMes.'%"')->queryScalar(); 
                            
                    $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();
                    $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar(); 


			$query = ControlProcesos::find()->distinct()
                                	->from('tbl_control_procesos')
	                                ->join('LEFT OUTER JOIN', 'tbl_control_params',
        	                                'tbl_control_procesos.evaluados_id = tbl_control_params.evaluados_id')
					->join('LEFT OUTER JOIN', 'tbl_arbols',
        	                                'tbl_control_params.arbol_id = tbl_arbols.id')
					->join('LEFT OUTER JOIN', 'tbl_usuarios',
        	                                'tbl_control_params.evaluados_id = tbl_usuarios.usua_id')
					->where(['tbl_control_procesos.anulado' => 'null'])
					->andwhere(['tbl_arbols.arbol_id' => 17])
					->andwhere(['like','tbl_control_procesos.tipo_corte', $txtMes])
                	                ->andwhere(['between','tbl_control_procesos.fechacreacion', $fechainiC, $fechafinC]);
                                
            
                }
                else
                {
                    $txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0 and responsable ='.Yii::$app->user->identity->id.' and tipo_corte like "%'.$txtMes.'%"')->queryScalar(); 

                    $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();
                    $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();  

                    $query = ControlProcesos::find()->distinct()                   
                        ->joinWith('ejecucionformularios')
                        ->joinWith('usuarios')
                        ->where(['tbl_control_procesos.anulado' => 'null'])
                        ->andwhere(['between','tbl_control_procesos.fechacreacion', $fechainiC, $fechafinC])
                        ->andwhere(['responsable' => Yii::$app->user->identity->id]);  

                }
            }
            else
            {
                if ($roles == "272" || $roles == "273" || $roles == "298") {
                    $txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0 and evaluados_id ='.Yii::$app->user->identity->id.' and tipo_corte like "%'.$txtMes.'%"')->queryScalar();             
                    
                    $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();
                    $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();  


                    $query = ControlProcesos::find()->distinct()                   
                            ->joinWith('ejecucionformularios')
                            ->joinWith('usuarios')
                            ->where(['tbl_control_procesos.anulado' => 'null'])
                            ->andwhere(['between','tbl_control_procesos.fechacreacion', $fechainiC, $fechafinC])
                            ->andwhere(['evaluados_id' => $sesiones]);
                }
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_control_procesos.evaluados_id' => $this->evaluados_id,
            'tbl_ejecucionformularios.created' => $this->fechacreacion,
        ]);

        $query->andFilterWhere(['like', 'tbl_control_procesos.evaluados_id', $this->evaluados_id]);
        $query->andFilterWhere(['like', 'tbl_ejecucionformularios.created', $this->fechacreacion]);

        return $dataProvider;
    }

    public function searchseguimientofecha($params)
    {
        $query = ControlProcesos::find()->distinct()                   
                    ->joinWith('ejecucionformularios')
                    ->joinWith('usuarios')
                    ->where(['tbl_control_procesos.tipo_corte' => $params])
		    ->andwhere(['tbl_control_procesos.anulado' => '0'])
                    ->andwhere(['tbl_control_procesos.responsable' => Yii::$app->user->identity->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_control_procesos.evaluados_id' => $this->evaluados_id,
        ]);

        $query->andFilterWhere(['like', 'tbl_control_procesos.evaluados_id', $this->evaluados_id]);

        return $dataProvider;
    }

}
