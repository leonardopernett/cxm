<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\base\Exception;

/**
 * This is the model class for table "tbl_roles".
 *
 * @property integer $role_id
 * @property string $role_nombre
 * @property string $role_descripcion
 * @property integer $per_cuadrodemando
 * @property integer $per_estadisticaspersonas
 * @property integer $per_hacermonitoreo
 * @property integer $per_reportes
 * @property integer $per_modificarmonitoreo
 * @property integer $per_adminsistema
 * @property integer $per_adminprocesos
 * @property integer $per_editarequiposvalorados
 * @property integer $per_inboxaleatorio
 */
class Roles extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['per_cuadrodemando', 'per_estadisticaspersonas',
            'per_hacermonitoreo', 'per_reportes', 'per_modificarmonitoreo',
            'per_adminsistema', 'per_adminprocesos',
            'per_editarequiposvalorados', 'per_inboxaleatorio', 'per_desempeno', 'per_abogado', 'per_jefeop', 'per_tecdesempeno', 'per_alertas', 'per_evaluacion','per_externo','per_directivo','per_asesormas','per_usuatlmast'], 'integer'],
            [['role_nombre', 'role_descripcion'], 'string', 'max' => 50],
            [['role_nombre', 'role_descripcion'], 'required'],
            [['role_nombre', 'role_descripcion'],'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'role_id' => Yii::t('app', 'Role ID'),
            'role_nombre' => Yii::t('app', 'Role Nombre'),
            'role_descripcion' => Yii::t('app', 'Role Descripcion'),
            'per_cuadrodemando' => Yii::t('app', 'Per Cuadrodemando'),
            'per_estadisticaspersonas' => Yii::t('app', 'Per Estadisticaspersonas'),
            'per_hacermonitoreo' => Yii::t('app', 'Per Hacermonitoreo'),
            'per_reportes' => Yii::t('app', 'Per Reportes'),
            'per_modificarmonitoreo' => Yii::t('app', 'Per Modificarmonitoreo'),
            'per_adminsistema' => Yii::t('app', 'Per Adminsistema'),
            'per_adminprocesos' => Yii::t('app', 'Per Adminprocesos'),
            'per_editarequiposvalorados' => Yii::t('app', 'Per Editarequiposvalorados'),
            'per_inboxaleatorio' => Yii::t('app', 'Per inboxaleatorio'),
            'per_desempeno' => Yii::t('app', 'Administrar desempeno'),
            'per_abogado' => Yii::t('app', 'Administrador Abogado'),
            'per_jefeop' => Yii::t('app', 'Jefe Operaciones'),
            'per_tecdesempeno' => Yii::t('app', 'Tecnico Desempeno'),
            'per_alertas' => Yii::t('app', 'Alertas Tecnico CX'),
            'per_evaluacion' => Yii::t('app', 'Evaluacion administrativos'),
            'per_externo' => Yii::t('app', 'Tecnico CX Externo'),
            'per_directivo' => Yii::t('app', 'Rol Directivo'),
            'per_asesormas' => Yii::t('app', 'Rol Asesor'),
            'per_usuatlmast' => Yii::t('app', 'Rol Usuario Tlmark/Ast'),
            
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelUsuariosRoles() {
        return $this->hasMany(RelUsuariosRoles::className(), ['rel_role_id' => 'role_id']);
    }

    /**
     * METODO YA NO SE USA, POR EL CAMBIO DE RELACION A GRUPOS DE USUARIO
     * @return \yii\db\ActiveQuery
     */
    public function getPermisosRolesArbols() {
        return $this->hasMany(PermisosRolesArbols::className(), ['role_id' => 'role_id']);
    }

    /**
     * Convierte el booleano en string
     * 
     * @param type $boolean
     * @return string
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getStringBoolean($boolean) {
        if ($boolean == 1) {
            return 'SI';
        } else {
            return 'NO';
        }
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

    /**
     * Funcion que genera el excel con los datos seleccionados en los filtros que tiene la vista
     *  index de logeventsadmin
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $models
     * @return boolean
     */
    public function generarReporteroles($models = null) {
        set_time_limit(0);
        $objPHPexcel = new \PHPExcel();
        $objPHPexcel->setActiveSheetIndex(0);
        $titulos = $this->attributeLabels();
        $column = 'A';
        $row = 2;
        try {
            foreach ($titulos as $titulo) {
                $objPHPexcel->getActiveSheet()->setCellValue($column . '1', $titulo);
                $column++;
            }

            for ($index = 0; $index < count($models); $index++) {
                $column = 'A';
                $model = $models[$index]->getAttributes();
                foreach ($model as $value) {
                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $value);
                    $column++;
                }
                $row++;
            }
            $self = Url::to(['roles/index']);
            header("refresh:1; url='$self'");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Reporte_roles.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = new \PHPExcel_Writer_Excel2007();
            $objWriter->setPHPExcel($objPHPexcel);
            $objWriter->save('php://output');
            Yii::$app->session->setFlash('success', Yii::t('app', 'Reporte generado exitosamente'));
            return true;
        } catch (Exception $exc) {
            return false;
        }
        return false;
    }

}
