<?php
namespace app\components\helpers\packageWidget\models;

use app\components\helpers\packageWidget\ObjectPack;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\BaseInflector;

/**
 * Абстрактная модель для моделей виджетов.
 * Class AbstractModel
 * @package app\components\helpers\packageWidget\models
 *
 * @param int $positionW
 * @param int $widgetId
 * @param int $id
 * @param string $title
 *
 * @param array $removeFiles
 * @param string $_imgExt
 */

abstract class AbstractModel extends Model
{
    public $positionW;
    public $widgetId;
    public $widgetName;
    private $id;
    public $title;

    protected $removeFiles;
    private $_imgExt = 'jpeg';

    /*
     * @return array the converted model attributes
     */
    abstract public static function convertContent($content);

    /*
     * @return json | string the mapped model attributes before save
     */
    abstract public function mapContent(&$uploadFiles);

    public function scenarios()
    {
        return [
            'create' => ['widgetId', 'positionW', 'title'],
            'edit' => ['widgetId', 'positionW', 'title'],
        ];
    }

    public function rules()
    {
        return [
            ['title', 'default', 'value' => ''],
            [
                'position', 'required',
                'message' => 'Позиция обязательна',
                'on' => ['create', 'edit']],
//            [
//                'title',
//                'string', 'min' => 3, 'max' => 255,
//                'tooShort' => 'Заголовок не может содержать менее 3 символов',
//                'tooLong' => 'Заголовок не может содержать более 255 символов',
//                'on' => ['create', 'edit']
//            ],
            ['title', 'titleValidate', 'on' => ['create', 'edit']]
        ];
    }

    public function titleValidate($attributes, $params){

        $this->$attributes = trim($this->$attributes);
        $strLen = mb_strlen($this->$attributes);
        if($this->$attributes && !($strLen >= 3 && $strLen <= 255)){
            $keyErr = 'w[' . $this->positionW .']['. $this->widgetName . '][' . $attributes . ']';
            $this->addError($keyErr, 'Длина поля от 3-x до 255 символов');
        }
    }

    /*
     * Add id
     * @param int $id the identity widget id
     */
    public function setId($id){
        $this->id = $id;
    }

    /*
     * @return int identity widget id
     */
    public function getId(){
        return $this->id;
    }

    /*
     * @return array the converted images name
     * @param array $files the new files with widget
     * @param array $oldFiles the oldFiles files with widget
     */
    public function prepareImages(array $files, $oldFiles = []){
        $arrName = [];
        $imgObj  = [];
        if($files){
            foreach ($files as $file){
                if($file instanceof UploadedFile){
                    $name = \Yii::$app->security->generateRandomString($length = 14) . '.' . $this->_imgExt;
                    $arrName[] = $name;
                    $imgObj[] = [
                        'obj' => $file,
                        'newName' => $name
                    ];
                }
            }
        }
        return ['images' => array_merge($arrName, $oldFiles), 'fileObj' => $imgObj];
    }

    /*
     * @return array the converted files name
     * @param array $files the new files with widget
     * @param array $oldFiles the oldFiles files with widget
     */
    public function prepareFiles(array $files, $oldFiles = []){
        $arrName = [];
        $fileObj  = [];
        if($files){
            foreach ($files as $file){
                if($file instanceof UploadedFile){

                    $baseName = $file->getBaseName();
                    $extension = $file->getExtension();
                    $originName = $baseName . '.' . $extension;
                    $slugName = self::transliteration($baseName) . '.' . $extension;

                    $arrName[] = ['origin' => $originName, 'translit' =>  $slugName];
                    $fileObj[] = [
                        'obj' => $file,
                        'newName' => $slugName
                    ];
                }
            }
        }
        return ['files' => array_merge($arrName, $oldFiles), 'fileObj' => $fileObj];
    }

    /*
    * Filtered attributes
    * @param mixed $attributes the model attributes
    * @param mixed $params the model params
    */
    public function filterOldFiles($attributes, $params){
        $attr = (!is_array($this->$attributes)) ? array_filter([$this->$attributes]) : $this->$attributes;
        $this->$attributes = array_diff($attr, ObjectPack::$removeFiles);
    }

    /*
     * @return string transliteration string
     * @param string $str the string to transliterated
     */
    public static function transliteration($str)
    {
        $transliteration = array(
            'А' => 'A', 'а' => 'a',
            'Б' => 'B', 'б' => 'b',
            'В' => 'V', 'в' => 'v',
            'Г' => 'G', 'г' => 'g',
            'Д' => 'D', 'д' => 'd',
            'Е' => 'E', 'е' => 'e',
            'Ё' => 'Yo', 'ё' => 'yo',
            'Ж' => 'Zh', 'ж' => 'zh',
            'З' => 'Z', 'з' => 'z',
            'И' => 'I', 'и' => 'i',
            'Й' => 'J', 'й' => 'j',
            'К' => 'K', 'к' => 'k',
            'Л' => 'L', 'л' => 'l',
            'М' => 'M', 'м' => 'm',
            'Н' => "N", 'н' => 'n',
            'О' => 'O', 'о' => 'o',
            'П' => 'P', 'п' => 'p',
            'Р' => 'R', 'р' => 'r',
            'С' => 'S', 'с' => 's',
            'Т' => 'T', 'т' => 't',
            'У' => 'U', 'у' => 'u',
            'Ф' => 'F', 'ф' => 'f',
            'Х' => 'H', 'х' => 'h',
            'Ц' => 'Cz', 'ц' => 'cz',
            'Ч' => 'Ch', 'ч' => 'ch',
            'Ш' => 'Sh', 'ш' => 'sh',
            'Щ' => 'Shh', 'щ' => 'shh',
            'Ъ' => 'ʺ', 'ъ' => 'ʺ',
            'Ы' => 'Y`', 'ы' => 'y`',
            'Ь' => '', 'ь' => '',
            'Э' => 'E`', 'э' => 'e`',
            'Ю' => 'Yu', 'ю' => 'yu',
            'Я' => 'Ya', 'я' => 'ya',
            '№' => '#', 'Ӏ' => '‡',
            '’' => '`', 'ˮ' => '¨',
        );

        $str = strtr($str, $transliteration);
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^0-9a-z\-\.\s]/', '', $str);
        $str = preg_replace('|([-\.\s]+)|s', '-', $str);
        $str = trim($str, '-');

        return $str;
    }
}