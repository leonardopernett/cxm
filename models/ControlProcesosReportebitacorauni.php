<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Controlbitacorauniv;
use yii\db\Query;

/**
 * ControlProcesosEquipos represents the model behind the search form about `app\models\ControlvocBloque1`.
 */
class ControlProcesosReportebitacorauni extends Controlbitacorauniv
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_bitacora_uni', 'id_momento', 'id_detalle_momento'], 'integer'],
            [['fecha_registro'], 'safe'],
            [['nombre', 'cedula', 'id_cliente', 'pcrc'], 'safe'],
        ];
    }
   
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function buscarbitacorauni($params)
    {

        $query = Controlbitacorauniv::find()->distinct()
            ->where("estado = 'abierto'")
            ->orderBy([
                'id_bitacora_uni' => SORT_DESC
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id_cliente' => $this->id_cliente,
            'pcrc' => $this->pcrc,
            'id_momento' => $this->id_momento,
            'id_detalle_momento' => $this->id_detalle_momento,
            'cedula' => $this->cedula,    
        ]);

        $txtFecha = explode(" ", $this->fecha_registro);
        $txtcanti = count($txtFecha);
        
         if ($txtcanti > 1) {
            $txtfechaini = $txtFecha[0];
            $txtfechafin = $txtFecha[2];
            $query->andFilterWhere(['like','id_cliente',$this->id_cliente])
            ->andFilterWhere(['like','pcrc',$this->pcrc])
            ->andFilterWhere(['like','id_momento',$this->id_momento])
            ->andFilterWhere(['like','id_detalle_momento',$this->id_detalle_momento])
            ->andFilterWhere(['like','cedula',$this->cedula])
            ->andFilterWhere(['between', 'fecha_registro', $txtfechaini, $txtfechafin]);
         }
         else
         {
            $query->andFilterWhere(['like','id_cliente',$this->id_cliente])
            ->andFilterWhere(['like','pcrc',$this->pcrc])
            ->andFilterWhere(['like','id_momento',$this->id_momento])
            ->andFilterWhere(['like','id_detalle_momento',$this->id_detalle_momento])
            ->andFilterWhere(['like','cedula',$this->cedula]);
         }      

        return $dataProvider;

    }   
    

}
