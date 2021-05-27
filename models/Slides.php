<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_slides".
 *
 * @property integer $id
 * @property string $titulo
 * @property string $descripcion
 * @property string $imagen
 * @property integer $activo
 * @property string $created
 * @property string $created_by
 * @property string $modified
 * @property string $modified_by
 */
class Slides extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_slides';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['imagen'], 'file',
                'extensions' => 'jpg, png',
                'mimeTypes' => 'image/jpeg, image/png',
            ],
            [['activo'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['titulo', 'created_by', 'modified_by'], 'string', 'max' => 100],
            [['descripcion', 'imagen'], 'string', 'max' => 255]
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
            'imagen' => Yii::t('app', 'Imagen'),
            'activo' => Yii::t('app', 'Activo'),
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
