<?php
namespace app\components\helpers\packageWidget\models;

use yii\web\UploadedFile;

/**
 * Модель по работе с данными виджета "Файлы".
 * Class Files
 * @package app\components\helpers\packageWidget\models
 *
 * @param string $files
 * @param array $oldFiles
 */

class Files extends AbstractModel {

    public $files;
    public $oldFiles;

    public function scenarios()
    {
        $scenarios = [
            'create' => ['files', 'oldFiles'],
            'edit' => ['files', 'oldFiles'],
        ];
        return array_merge_recursive(parent::scenarios(), $scenarios);
    }

    public function rules()
    {
        $rules = [
            ['oldFiles', 'default', 'value' => []],
            [
                'oldFiles', 'filterOldFiles',
                'on' => 'edit'
            ],
            [
                'files', 'required',
                'message' => 'Пожалуста, загрузите файлы',
                'when' => function($model){ return (empty($model->files) && empty($model->oldFiles)); },
                'on' => ['create', 'edit']
            ],
            [
                ['files'], 'file', 'extensions' => ['doc', 'docx', 'pdf', 'xls', 'xlsx'],
                'maxFiles' => 20,
                'tooMany' => 'Вы можете загрузить одновременно максимум 20 файла',
                'wrongExtension' => 'Неверный формат файла(лов)',
                'checkExtensionByMimeType' => false,
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
        $this->oldFiles = [];
        $this->attributes = $attr;
        $this->files = UploadedFile::getInstancesByName('w[' . $this->positionW . '][wFiles]');
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
        $data = $this->prepareFiles($this->files, $this->oldFiles);
        $uploadFiles = array_merge($uploadFiles, $data['fileObj']);
        return json_encode($data['files'], JSON_UNESCAPED_UNICODE);
    }

    /*
     * @param json $data the widget content
     * @return array returned converted content for widget
     */
    public static function convertContent($data){
        $content = [];
        $files = json_decode($data['content'], JSON_UNESCAPED_UNICODE);
        foreach ($files as $file){
            $pathToFile = \Yii::getAlias('@webroot') .  $data['filePath'] . '/' . $file['translit'];
            if(file_exists($pathToFile)){
                $content[] = [
                    'url'      => $data['filePath'] . '/' . $file['translit'],
                    'translit' => $file['translit'],
                    'origin'   => $file['origin']
                ];
            }
        }
        return $content;
    }
}