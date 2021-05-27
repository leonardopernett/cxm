<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\mssql\PDO;
/**
 * This is the model class for table "distribucion_meucci_asesores".
 *
 * @property string $mes
 * @property string $dmeNumeroDocumento
 * @property string $usuario
 * @property integer $empEmpleadoId
 * @property string $Empleado
 * @property string $tdcAbreviatura
 * @property string $TipoCargoEmpleado
 * @property string $Cliente
 * @property integer $CodigoCliente
 * @property string $Servicio
 * @property integer $CodigoServicio
 * @property string $Programa
 * @property integer $CodigoPrograma
 * @property string $PCRC
 * @property string $CodigoPCRC
 * @property integer $CentroCosto
 * @property string $Ciudad
 * @property string $FechaIngreso
 * @property string $FechaConexUltimoPCRC
 * @property string $UsuarioWindows
 * @property string $Agente_Principal
 * @property string $Usuario_Genesys
 * @property string $Cod_Logueo_Genesys
 * @property string $Cod_Logueo_Avaya
 * @property string $Usuario_Avaya
 * @property string $EstadoEmpleado
 * @property string $dmeNumeroDocumentoSup6
 * @property string $TipoCargoDescripcionSup6
 * @property string $cgoDescripcionSup6
 * @property string $empEmpleadoN6
 * @property string $UsuarioWindowsSup6
 * @property integer $CentroCostoSup6
 * @property string $dmeNumeroDocumentoSup5
 * @property string $TipoCargoDescripcionSup5
 * @property string $cgoDescripcionSup5
 * @property string $empEmpleadoN5
 * @property string $UsuarioWindowsSup5
 * @property integer $CentroCostoSup5
 * @property string $dmeNumeroDocumentoSup4
 * @property string $TipoCargoDescripcionSup4
 * @property string $cgoDescripcionSup4
 * @property string $empEmpleadoN4
 * @property string $UsuarioWindowsSup4
 * @property integer $CentroCostoSup4
 * @property string $dmeNumeroDocumentoSup3
 * @property string $TipoCargoDescripcionSup3
 * @property string $cgoDescripcionSup3
 * @property string $empEmpleadoN3
 * @property string $UsuarioWindowsSup3
 * @property integer $CentroCostoSup3
 * @property integer $dmeNumeroDocumentoSup2
 * @property string $TipoCargoDescripcionSup2
 * @property string $cgoDescripcionSup2
 * @property string $empEmpleadoN2
 * @property string $UsuarioWindowsSup2
 * @property integer $CentroCostoSup2
 * @property integer $dmeNumeroDocumentoSup1
 * @property string $TipoCargoDescripcionSup1
 * @property string $cgoDescripcionSup1
 * @property string $empEmpleadoN1
 * @property string $UsuarioWindowsSup1
 * @property integer $CentroCostoSup1
 */
class Equipoteo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribucion_meucci_asesores';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbTeo');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mes', 'dmeNumeroDocumento', 'usuario', 'CodigoPCRC'], 'required'],
            [['mes', 'FechaIngreso', 'FechaConexUltimoPCRC'], 'safe'],
            [['dmeNumeroDocumento', 'empEmpleadoId', 'CodigoCliente', 'CodigoServicio', 'CodigoPrograma', 'CentroCosto', 'dmeNumeroDocumentoSup6', 'CentroCostoSup6', 'dmeNumeroDocumentoSup5', 'CentroCostoSup5', 'dmeNumeroDocumentoSup4', 'CentroCostoSup4', 'dmeNumeroDocumentoSup3', 'CentroCostoSup3', 'dmeNumeroDocumentoSup2', 'CentroCostoSup2', 'dmeNumeroDocumentoSup1', 'CentroCostoSup1'], 'integer'],
            [['usuario'], 'string', 'max' => 25],
            [['Empleado', 'Programa', 'PCRC', 'Cod_Logueo_Genesys', 'empEmpleadoN6', 'empEmpleadoN5', 'empEmpleadoN4', 'empEmpleadoN3', 'empEmpleadoN2', 'empEmpleadoN1'], 'string', 'max' => 60],
            [['tdcAbreviatura'], 'string', 'max' => 11],
            [['TipoCargoEmpleado'], 'string', 'max' => 30],
            [['Cliente', 'TipoCargoDescripcionSup6', 'TipoCargoDescripcionSup5', 'TipoCargoDescripcionSup4', 'TipoCargoDescripcionSup3', 'TipoCargoDescripcionSup2', 'TipoCargoDescripcionSup1'], 'string', 'max' => 40],
            [['Servicio', 'cgoDescripcionSup6', 'cgoDescripcionSup5', 'cgoDescripcionSup4', 'cgoDescripcionSup3', 'cgoDescripcionSup2', 'cgoDescripcionSup1'], 'string', 'max' => 50],
            [['CodigoPCRC', 'EstadoEmpleado'], 'string', 'max' => 15],
            [['Ciudad'], 'string', 'max' => 20],
            [['UsuarioWindows', 'Agente_Principal', 'Usuario_Genesys', 'Cod_Logueo_Avaya', 'UsuarioWindowsSup6', 'UsuarioWindowsSup5', 'UsuarioWindowsSup4', 'UsuarioWindowsSup3', 'UsuarioWindowsSup2', 'UsuarioWindowsSup1'], 'string', 'max' => 55],
            [['Usuario_Avaya'], 'string', 'max' => 75]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mes' => 'Mes',
            'dmeNumeroDocumento' => 'Dme Numero Documento',
            'usuario' => 'Usuario',
            'empEmpleadoId' => 'Emp Empleado ID',
            'Empleado' => 'Empleado',
            'tdcAbreviatura' => 'Tdc Abreviatura',
            'TipoCargoEmpleado' => 'Tipo Cargo Empleado',
            'Cliente' => 'Cliente',
            'CodigoCliente' => 'Codigo Cliente',
            'Servicio' => 'Servicio',
            'CodigoServicio' => 'Codigo Servicio',
            'Programa' => 'Programa',
            'CodigoPrograma' => 'Codigo Programa',
            'PCRC' => 'Pcrc',
            'CodigoPCRC' => 'Codigo Pcrc',
            'CentroCosto' => 'Centro Costo',
            'Ciudad' => 'Ciudad',
            'FechaIngreso' => 'Fecha Ingreso',
            'FechaConexUltimoPCRC' => 'Fecha Conex Ultimo Pcrc',
            'UsuarioWindows' => 'Usuario Windows',
            'Agente_Principal' => 'Agente  Principal',
            'Usuario_Genesys' => 'Usuario  Genesys',
            'Cod_Logueo_Genesys' => 'Cod  Logueo  Genesys',
            'Cod_Logueo_Avaya' => 'Cod  Logueo  Avaya',
            'Usuario_Avaya' => 'Usuario  Avaya',
            'EstadoEmpleado' => 'Estado Empleado',
            'dmeNumeroDocumentoSup6' => 'Dme Numero Documento Sup6',
            'TipoCargoDescripcionSup6' => 'Tipo Cargo Descripcion Sup6',
            'cgoDescripcionSup6' => 'Cgo Descripcion Sup6',
            'empEmpleadoN6' => 'Emp Empleado N6',
            'UsuarioWindowsSup6' => 'Usuario Windows Sup6',
            'CentroCostoSup6' => 'Centro Costo Sup6',
            'dmeNumeroDocumentoSup5' => 'Dme Numero Documento Sup5',
            'TipoCargoDescripcionSup5' => 'Tipo Cargo Descripcion Sup5',
            'cgoDescripcionSup5' => 'Cgo Descripcion Sup5',
            'empEmpleadoN5' => 'Emp Empleado N5',
            'UsuarioWindowsSup5' => 'Usuario Windows Sup5',
            'CentroCostoSup5' => 'Centro Costo Sup5',
            'dmeNumeroDocumentoSup4' => 'Dme Numero Documento Sup4',
            'TipoCargoDescripcionSup4' => 'Tipo Cargo Descripcion Sup4',
            'cgoDescripcionSup4' => 'Cgo Descripcion Sup4',
            'empEmpleadoN4' => 'Emp Empleado N4',
            'UsuarioWindowsSup4' => 'Usuario Windows Sup4',
            'CentroCostoSup4' => 'Centro Costo Sup4',
            'dmeNumeroDocumentoSup3' => 'Dme Numero Documento Sup3',
            'TipoCargoDescripcionSup3' => 'Tipo Cargo Descripcion Sup3',
            'cgoDescripcionSup3' => 'Cgo Descripcion Sup3',
            'empEmpleadoN3' => 'Emp Empleado N3',
            'UsuarioWindowsSup3' => 'Usuario Windows Sup3',
            'CentroCostoSup3' => 'Centro Costo Sup3',
            'dmeNumeroDocumentoSup2' => 'Dme Numero Documento Sup2',
            'TipoCargoDescripcionSup2' => 'Tipo Cargo Descripcion Sup2',
            'cgoDescripcionSup2' => 'Cgo Descripcion Sup2',
            'empEmpleadoN2' => 'Emp Empleado N2',
            'UsuarioWindowsSup2' => 'Usuario Windows Sup2',
            'CentroCostoSup2' => 'Centro Costo Sup2',
            'dmeNumeroDocumentoSup1' => 'Dme Numero Documento Sup1',
            'TipoCargoDescripcionSup1' => 'Tipo Cargo Descripcion Sup1',
            'cgoDescripcionSup1' => 'Cgo Descripcion Sup1',
            'empEmpleadoN1' => 'Emp Empleado N1',
            'UsuarioWindowsSup1' => 'Usuario Windows Sup1',
            'CentroCostoSup1' => 'Centro Costo Sup1',
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Equipoteo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'detexto', $this->detexto]);

        return $dataProvider;
    }

    public static function findEquipoteo($identificacion = 0, $fecha, $cliente)
    {
        $sql = "SELECT mes, TRIM(usuario) as usuario, Empleado, Cliente, empEmpleadoN6, UsuarioWindowsSup6, dmeNumeroDocumentoSup6, dmeNumeroDocumento FROM teo.distribucion_meucci_asesores WHERE mes = :fecha
        and dmeNumeroDocumentoSup6 = :identificacion and Cliente = :cliente";
        $command = \Yii::$app->get('dbTeo')->createCommand($sql);
        $command->bindParam(":identificacion", $identificacion);
        $command->bindParam(":fecha", $fecha);
        $command->bindParam(":cliente", $cliente);
        $users                           = $command->queryAll();
        if ($users) 
        {
            return $users;
        }
        return null;
    }

    public static function findAllTeo($fecha)
    {
            $sql = "SELECT mes, usuario, Empleado, Cliente, empEmpleadoN6, UsuarioWindowsSup6, dmeNumeroDocumentoSup6 FROM teo.distribucion_meucci_asesores WHERE mes = :fecha";
            $command = \Yii::$app->get('dbTeo')->createCommand($sql);
            $command->bindParam(":fecha", $fecha);
            $users                           = $command->queryAll();
        if ($users) {
            return $users;
        }

        return null;
    }

    public static function getLideres($fecha)
    {
            $sql = "SELECT DISTINCT empEmpleadoN6, Cliente, dmeNumeroDocumentoSup6 FROM teo.distribucion_meucci_asesores WHERE mes = :fecha and Cliente <> ''";
            $command = \Yii::$app->get('dbTeo')->createCommand($sql);
            $command->bindParam(":fecha", $fecha);
            $users                           = $command->queryAll();
        if ($users) {
            return $users;
        }

        return null;
    }
}