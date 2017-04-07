<?php

namespace app\components\helpers\packageWidget;

use Yii;
use yii\base\Model;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use app\components\helpers\packageWidget\models\Main;
use yii\web\UploadedFile;

/*
 * Главнй класс для работы с контентом виджетов.
 * Class ObjectPack
 * @package app\components\helpers\packageWidget
 *
 * @property array  $_info
 * @property array  $_validObject
 * @property array  $_wErrors
 * @property string $_action
 * @property string $_dirFiles
 * @property array  $_uploadFiles
 *
 * @property array  $removeFiles
 */

class ObjectPack extends Object {

    private $_info;
    private $_validObject = [];
    private $_wErrors = [];
    private $_action;
    private $_dirFiles;
    private $_uploadFiles = [];

    public static $removeFiles;

    const BASE_DIR = 'res/';

    /*
     * init function __construct
     * @param sting $action
     */
    public function __construct($action = 'create')
    {
        Yii::$app->widgetPackage->loadModels();
        $info = Yii::$app->widgetPackage->getInfo();
        $this->_info = ArrayHelper::map($info, 'widget', 'widget_id');
        $this->_action = $action;
        parent::__construct();
    }

    /*
     * get contend by identity is action and identityId is content id
     * @param string $identity
     * @param int    $identityId
     * @return array
     */
    public static function getContent(string $identity, int $identityId){
        $model = (new Main)->initModel($identity);
        return $model->findContentById($identityId);
    }

    /*
     * initialize widget content validation with POST
     * @return bool
     */
    public function initValidate(){
        $wParams = Yii::$app->request->post('w');

        $this->mapRemoveImg();
        if($wParams){
            foreach ($wParams as $position => $wValue){
                $widgetName = key($wValue);
                $widgetId = $this->_info[$widgetName];
                if($model = clone Yii::$app->widgetPackage->getModel($widgetId)){
                    $model->scenario  = $this->_action;
                    $model->positionW = $position;
                    $model->widgetId  = $widgetId;
                    $model->widgetName = $widgetName;
                    $model->id = $wValue[$widgetName]['id'];
                    $this->initValid($model, $wValue[$widgetName], $widgetName);
                }
            }
        }
        return ($this->_wErrors) ? false : true;
    }

    /*
     * validation model widget content
     * @param yii\base\Model $model
     * @param array $attr is POST attributes
     * @param string $key is widget  name
     */
    private function initValid(Model $model, $attr, $key){

        if($model->validateWidgetAttr($attr)){
            $this->_validObject[] = clone $model;
        } else {
            $this->_wErrors = array_merge($this->_wErrors , $model->getErrors());
        }
    }

    /*
     * save widgets content
     * @param sting $identity is name action
     * @param int   $identityId is action id
     */
    public function saveContent(string $identity, int $identityId){
        $model = (new Main)->initModel($identity);
        $this->setDirFiles($identity, $identityId);
        if($model->saveContent($this->_validObject, $identityId, $this->_uploadFiles)){
            $this->uploadFiles();
            $this->removeFiles();
        }
    }

    /*
     * delete widgets content
     * @param sting $identity is name action
     * @param int   $identityId is action id
     */
    public function removeContent(string $identity, int $identityId){
        $model = (new Main)->initModel($identity);
        $this->setDirFiles($identity, $identityId);
        if($model::deleteAll([$identity . '_id' => $identityId])){
            $dir = $this->getDirFiles();
            $this->removeAllFiles($dir);
        }
    }

    /*
     * delete old images
     */
    private function mapRemoveImg(){
        $removeImg = \Yii::$app->request->post('removeImg', []);
        $removeFile = \Yii::$app->request->post('removeFile', []);
        $map = [];
        $removeFiles = array_merge($removeImg, $removeFile);
        if($removeFiles && is_array($removeImg) && is_array($removeFile)){
            $map = array_map(function ($value){
                return  substr($value, strrpos($value, '/') + 1);
            }, $removeFiles);
        }

        self::$removeFiles = $map;
    }

    /*
     * get widget errors
     * @return array
     */
    public function getWidgetErrors(){
        return $this->_wErrors;
    }

    /*
     * delete all widgets files
     * @param string $dir
     */
    private function removeAllFiles($dir){
        if ($objs = glob($dir . "/*")) {
            foreach($objs as $obj) {

                is_dir($obj) ? $this->removeAllFiles($obj) : unlink($obj);
            }
        }
        if(is_dir($dir)) rmdir($dir);
    }

    /*
     * upload files
     */
    private function uploadFiles(){
        $dir = $this->getDirFiles();
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        foreach ($this->_uploadFiles as $file){
            if($file['obj'] instanceof UploadedFile && isset($file['newName'])){
                $pathToFile = $dir . '/' .  $file['newName'];
                $file['obj']->saveAs($pathToFile);
            }
        }
    }

    /*
     * delete some files
     */
    private function removeFiles(){
        $dir = $this->getDirFiles();
        if(self::$removeFiles){
            foreach (self::$removeFiles as $file){
                $pathToFile = $dir . '/' .  $file;
                if(file_exists($pathToFile)){
                    unlink($pathToFile);
                }
            }
        }
    }

    /*
     * get dir by widgets
     */
    public function getDirFiles(){
        return $this->_dirFiles;
    }

    /*
     * set files dir to property $_dirFiles
     */
    public function setDirFiles($identity, $identityId){
        $this->_dirFiles = \Yii::getAlias('@webroot') . '/' . self::BASE_DIR . $identity . '/' . $identityId;
    }
}