<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class ImageUploadForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $images;

    public function rules()
    {
        return [
            [
                ['images'],
                'file',
                'skipOnEmpty' => false,
                'maxFiles' => 10,
                'extensions' => 'jpg, jpeg, png',
                'maxSize' => 5 * 1024 * 1024
            ],
        ];
    }

    public function upload()
    {
        $result = false;
        if ($this->validate()) {
            foreach ($this->images as $file) {
                // Сохраняем файлы на сервер
                $path = 'uploads/';
                $filename = $file->baseName . '.' . $file->extension;
                $result = $file->saveAs($path . $filename, $file->tempName);
            }
        }
        return $result;
    }
}
