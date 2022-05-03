<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_control_procesos".
 *
 * @property integer $id
 * @property integer $evaluados_id
 * @property string $salario
 * @property string $tipo_corte
 * @property string $responsable
 * @property string $cant_valor
 * @property string $Dedic_valora
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $idtc
 */
class ControlProcesosPlan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_control_procesos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['evaluados_id', 'anulado', 'idtc'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['salario', 'tipo_corte', 'responsable'], 'string', 'max' => 150],
            [['cant_valor', 'Dedic_valora'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', ''),
            'evaluados_id' => Yii::t('app', ''),
            'salario' => Yii::t('app', ''),
            'tipo_corte' => Yii::t('app', ''),
            'responsable' => Yii::t('app', ''),
            'cant_valor' => Yii::t('app', ''),
            'Dedic_valora' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'idtc' => Yii::t('app', ''),
        ];
    }


    public function buscarcontrolplan($params){
        $sesiones =Yii::$app->user->identity->id;

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

        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));
     
        $varfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $varfechafin = date('Y-m-d', mktime(0,0,0, $month, $day, $year));
        
        if ($roles == "270" || $roles == "309" || $sesiones == '1173' || $sesiones == '2887' || $sesiones == '3430' || $sesiones == '2652' || $sesiones == '189') {
            $query = ControlProcesos::find()->distinct()                   
                    ->joinWith('ejecucionformularios')
                    ->joinWith('usuarios')
                    ->where(['tbl_control_procesos.anulado' => 'null'])
                    ->andwhere(['between','tbl_control_procesos.fechacreacion', $varfechainicio, $varfechafin]);
        }else{
            if ($roles == "274" || $roles == "276" || $roles == "311") {
                $vartipopermiso = Yii::$app->db->createCommand("select tipopermiso from tbl_plan_permisos where anulado = 0 and usuaidpermiso = $sesiones")->queryScalar();
                $vararbol = Yii::$app->db->createCommand("select arbol_id from tbl_plan_permisos where anulado = 0 and usuaidpermiso = $sesiones")->queryScalar();

                if ($vartipopermiso == 1) {
                    $query = ControlProcesos::find()->distinct()
                                        ->from('tbl_control_procesos')
                                        ->join('LEFT OUTER JOIN', 'tbl_control_params',
                                                'tbl_control_procesos.evaluados_id = tbl_control_params.evaluados_id')
                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                'tbl_control_params.arbol_id = tbl_arbols.id')
                        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                'tbl_control_params.evaluados_id = tbl_usuarios.usua_id')
                        ->where(['tbl_control_procesos.anulado' => 'null'])
                        ->andwhere(['tbl_arbols.arbol_id' => $vararbol])
                        ->andwhere(['between','tbl_control_procesos.fechacreacion', $varfechainicio, $varfechafin]);
                }else{
                    $query = ControlProcesos::find()->distinct()                   
                        ->joinWith('ejecucionformularios')
                        ->joinWith('usuarios')
                        ->where(['tbl_control_procesos.anulado' => 'null'])
                        ->andwhere(['between','tbl_control_procesos.fechacreacion', $varfechainicio, $varfechafin])
                        ->andwhere(['responsable' => Yii::$app->user->identity->id]);  
                }
            }else{
                if ($roles == "272" || $roles == "273") {
                    $query = ControlProcesos::find()->distinct()                   
                            ->joinWith('ejecucionformularios')
                            ->joinWith('usuarios')
                            ->where(['tbl_control_procesos.anulado' => 'null'])
                            ->andwhere(['between','tbl_control_procesos.fechacreacion', $varfechainicio, $varfechafin])
                            ->andwhere(['evaluados_id' => $sesiones]);
                }else{
                    $vartipopermiso = Yii::$app->db->createCommand("select tipopermiso from tbl_plan_permisos where anulado = 0 and usuaidpermiso = $sesiones")->queryScalar();
                    $vararbol = Yii::$app->db->createCommand("select arbol_id from tbl_plan_permisos where anulado = 0 and usuaidpermiso = $sesiones")->queryScalar();

                    if ($vartipopermiso == 1) {
                        $query = ControlProcesos::find()->distinct()
                                            ->from('tbl_control_procesos')
                                            ->join('LEFT OUTER JOIN', 'tbl_control_params',
                                                    'tbl_control_procesos.evaluados_id = tbl_control_params.evaluados_id')
                            ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_control_params.arbol_id = tbl_arbols.id')
                            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                                    'tbl_control_params.evaluados_id = tbl_usuarios.usua_id')
                            ->where(['tbl_control_procesos.anulado' => 'null'])
                            ->andwhere(['tbl_arbols.arbol_id' => $vararbol])
                            ->andwhere(['between','tbl_control_procesos.fechacreacion', $varfechainicio, $varfechafin]);
                    }
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

    public function getplanrol($params){
        $data = Yii::$app->db->createCommand("select r.role_nombre from tbl_roles r inner join rel_usuarios_roles ur on r.role_id = ur.rel_role_id  inner join tbl_usuarios u on ur.rel_usua_id = u.usua_id where u.usua_id = $params")->queryScalar();

        return $data;
    }
}