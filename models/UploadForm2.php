<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use Yii;

class UploadForm2 extends Model
{
        /**
     * @var UploadedFile
     */
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'png, jpg, pdf, xls, xlsx, xlt, csv, docx', 'maxSize'=> 1024 * 1024 * 50],
        ];
    }   

}