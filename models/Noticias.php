<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_noticias".
 *
 * @property integer $id
 * @property string $titulo
 * @property string $descripcion
 * @property integer $activa
 * @property string $created
 * @property string $created_by
 * @property string $modified
 * @property string $modified_by
 */
class Noticias extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_noticias';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['titulo', 'descripcion'], 'required'],
            [['descripcion'], 'string'],
            [['activa'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['titulo'], 'string', 'max' => 255],
            [['created_by', 'modified_by'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'titulo' => Yii::t('app', 'Titulo'),
            'descripcion' => Yii::t('app', 'Descripcion'),
            'activa' => Yii::t('app', 'Activa'),
            'created' => Yii::t('app', 'Created'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified' => Yii::t('app', 'Modified'),
            'modified_by' => Yii::t('app', 'Modified By'),
        ];
    }

    /**
     * Método que me controla la traza de modificaciones de las noticias
     * 
     * @param boolean $insert whether this method called while inserting a record.
     * If false, it means the method is called while updating a record.
     * @return boolean whether the insertion or updating should continue.
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created = date("Y-m-d H:i:s");
                $this->created_by = Yii::$app->user->identity->fullName;
                $this->modified = date("Y-m-d H:i:s");                
                $this->modified_by = Yii::$app->user->identity->fullName;
            } else {
                $this->modified = date("Y-m-d H:i:s");
                $this->modified_by = Yii::$app->user->identity->fullName;
            }
            return true;
        } else {
            return false;
        }
    }

}
