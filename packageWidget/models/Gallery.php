<?php
namespace app\components\helpers\packageWidget\models;

use yii\web\UploadedFile;
/**
 * Модель по работе с данными виджета "Геллерея".
 * Class Gallery
 * @package app\components\helpers\packageWidget\models
 *
 * @param array $images instances of UploadedFile
 * @param array $oldFiles
 */

class Gallery extends AbstractModel {

    public $images;
    public $oldImages;

    public function scenarios()
    {
        $scenarios = [
            'create' => ['images', 'oldImages'],
            'edit' => ['images', 'oldImages']
        ];
        return array_merge_recursive(parent::scenarios(), $scenarios);
    }

    public function rules()
    {
        $rules = [
            ['oldImages', 'default', 'value' => []],
            [
                'oldImages', 'filterOldFiles',
                'on' => 'edit'
            ],
            [
                'images', 'required',
                'message' => 'Пожалуста, загрузите фотографии',
                'when' => function($model){ return (empty($model->images) && empty($model->oldImages)) ? true :false; },
                'on' => ['create', 'edit']
            ],
            [
                'images',
                'file', 'maxSize' => 1048576, 'mimeTypes' => ['image/jpeg', 'image/png'],
                'message'       => 'Ошибка загрузки файла',
                'tooBig'        => 'Размер фото должен быть до 1Мб',
                'wrongMimeType' => 'Неверный формат файла',
                'uploadRequired' => 'Пожалуста, загрузите фотографии',
                'maxFiles' => 10,
                'on' => ['create', 'edit']
            ],
        ];

        return array_merge_recursive(parent::rules(), $rules);
    }

    /*
    * @param string $attr the POST attributes for validation
    * @return bool returned result validation model attributes
    */
    public function validateWidgetAttr($attr){
        $this->oldImages = [];
        $this->attributes = $attr;
        $this->images = UploadedFile::getInstancesByName('w[' . $this->positionW . '][wGallery]');
        if (!$this->validate()) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * @param array &$uploadFiles the ref with ObjectPack
     * @return json returned result remapped attributes
     */
    public function mapContent(&$uploadFiles){
        $data = $this->prepareImages($this->images, $this->oldImages);
        $uploadFiles = array_merge($uploadFiles, $data['fileObj']);
        return json_encode($data['images'], JSON_UNESCAPED_UNICODE);
    }

    /*
     * @param json $data the widget content
     * @return array returned converted content for widget
     */
    public static function convertContent($data){
        $content = [];
        $images = json_decode($data['content']);
        foreach ($images as $image){
            $pathToFile =  \Yii::getAlias('@webroot') .  $data['filePath'] . '/' . $image;
            if(file_exists($pathToFile)){
                $url = $data['filePath'] . '/' . $image;
                $content[] = ['url' => $url, 'imageName' => $image];
            }
        }
        return $content;
    }
}