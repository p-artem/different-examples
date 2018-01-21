<?php

namespace console\extensions\fileService;
use console\controllers\FileController;
use yii\console\Exception;
use yii\helpers\FileHelper;
use yii\helpers\Console;

/*
 * Class  FileService
 *
 * @property string $error
 * @property object $object console\controllers\FileController
 * @property int $cntReplace
 * @property string $driver has driver namespace
 * @property string $pattern
 * @property array $fileHelp
 * @property array $fileMimeTypes
 * @property array $driverName
 */

class FileService implements ServiceInterface {

    private $error = false;
    private $object;

    public $cntReplace = 0;
    public $driver = false;
    public $pattern = '/(%1$s)+/u';

    const MIN_PARAM = 3;

    public $fileHelp = [
        'paramTextHelp' => [
            'f' => 'Must contain the path to the file that you want to change',
            's' => 'Must contain phrase for change',
            'r' => 'Must contain phrase to replace'
        ],
        'error' => [
            'f' => [
                'notFound'  => 'File not found, please check your path',
                'mimeType'  => 'Mime Type is not supported',
                'empty'     => 'File is empty',
                'write'     => 'File is not writable',
                'notDriver' => 'Driver not found, please check settings',
            ]
        ]
    ];

    private $fileMimeTypes = [
        'txt'  => 'text/plain',
        'htm'  => 'text/html',
        'html' => 'text/html',
        'json' => 'application/json',
        'xml'  => 'application/xml',
    ];

    private $driverName = [
        'text' => ['txt', 'htm', 'html', 'json'],
        'xml'  => ['xml'],
    ];

    /*
     * @param FileController $controller
     */
    public function __construct(FileController $controller)
    {
        $this->object = $controller;
    }

    /*
     * Check arguments on empty
     * @return bool
     */
    public function isEmptyArguments(){
        $cnt = 0;
        foreach ($this->object->optionAliases() as $param){
            if($this->object->$param) $cnt  += 1;
        }
        return ($cnt < self::MIN_PARAM) ?  true : false;
    }

    /*
     * Check arguments for some methods
     * @return bool
     */
    public function checkArguments(){
        if(!$this->checkFile($this->object->file)) {
            $this->object->stdout("Error:". $this->getSpace() . $this->error . "\n", Console::FG_RED);
            return false;
        }
        return true;
    }

    /*
     * Check file
     * @return bool
     */
    private function checkFile($path){
        $this->object->file =  FileHelper::normalizePath($path);
        if(!file_exists($this->object->file)) return $this->setError('f', 'notFound');

        $itemMimeType = FileHelper::getMimeType($this->object->file);
        if(null == $keyType = array_search($itemMimeType, $this->fileMimeTypes))
            return $this->setError('f', 'mimeType', $itemMimeType);

        $func = function($keyType){
            foreach ($this->driverName as $keyD => $valD){
                if(in_array($keyType, $valD)) return __NAMESPACE__ . '\\' . ucfirst($keyD) . 'Driver';
            }
            return false;
        };

        if(!$this->driver = $func($keyType))  return  $this->setError('f', 'notDriver');
        if(!@file($this->object->file))       return $this->setError('f', 'empty');
        if(!is_writable($this->object->file)) return $this->setError('f', 'write');

        return true;
    }

    /*
     * @param string $param argument name
     * @param string $flag is key in @property fileHelp
     * @param string|int $secondText has additional information
     * @return bool
     */
    private function setError($param, $flag, $secondText = ''){
        $this->error = (isset($this->fileHelp['error'][$param][$flag]))
            ? $this->fileHelp['error'][$param][$flag] . (($secondText) ? ' - ' . $secondText : '')
            : 'Some error with param ' . $param;
        return false;
    }

    /*
     * Generate helper text for arguments
     */
    public function helper() {
        $this->object->stdout("Parser parameters:\n", Console::FG_GREEN);
        foreach ($this->object->optionAliases() as $keyP => $valP){
            $this->object->stdout($this->getSpace(4) . "-" . $keyP, Console::FG_YELLOW);
            $this->object->stdout($this->getSpace(3) . $this->fileHelp['paramTextHelp'][$keyP] . "\n", Console::FG_GREY);
        }
    }

    /*
     *  Generate information if driver modify file
     */
    public function replaceResult(){
        $this->object->stdout("Success:". $this->getSpace() . "File is update\n", Console::FG_GREEN);
        $this->object->stdout("Count phrase to update:". $this->getSpace() . $this->cntReplace . "\n", Console::FG_CYAN);
    }

    /*
     * Create string consist with some count space
     * @param int $cnt
     * @return string
     */
    private function getSpace($cnt = 1){
        $space = ' ';
        for($i = 1; $i < $cnt; $i++) $space .= ' ';
        return $space;
    }

    /*
     * @return object FileController $controller
     */
    public function getObject(){
        return $this->object;
    }
}