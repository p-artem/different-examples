<?php
namespace console\controllers;

use Yii;
use console\extensions\fileService\FileService;
use yii\console\Controller;
use yii\helpers\Console;

/*
 * The application of text modification by pattern
 *
 * @property string $file
 * @property string $search
 * @property string $replace
 * @property object $service console\extensions\fileService\FileService
 */
class FileController extends Controller
{
    public $file;
    public $search;
    public $replace;

    private $service;

    /*
     * Options method
     * @var string $actionID action name
     * @return array
     */
    public function options($actionID)
    {
        return ['file', 'search', 'replace'];
    }

    /*
     * Option aliases
     * @return array
     */
    public function optionAliases()
    {
        return ['f' => 'file','s' => 'search','r' => 'replace'];
    }

    /*
     * index action
     * @return int|bool
     */
    public function actionIndex() {

        $this->service = new FileService($this);
        if($this->service->isEmptyArguments())return $this->service->helper();
        if(!$this->service->checkArguments()) return false;
        $this->loadDriver();
        return 1;
    }

    /*
     * load some Driver
     * @return bool
     */
    private function loadDriver(){
        if(!$class = $this->service->driver){
            $this->stdout("Error: Class for driver Not Found - ". $this->service->driver . "\n", Console::FG_RED);
            return false;
        }
        /*
         * @var object instanceof console\extensions\fileService\AbstractDriver
         */
        $driver = new $class($this->service);
        $driver->replaceData();
        $this->service->replaceResult();
        return true;
    }

    /*
     * action Help
     * @return int|bool
     */
    public function actionHelp() {
        $this->service = new FileService($this);
        $this->service->helper();
        return 1;
    }
}