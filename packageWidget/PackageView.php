<?php
namespace app\components\helpers\packageWidget;

use yii\base\Widget;
use Yii;

class PackageView extends Widget
{
    private $view = 'index';
    public $widgetView;
    public $data = [];
    public $identity = false;
    public $identityId = 0;
    public $content = false;
    public $listNames = [];
    public $widgetParams = [];

    /*
     * initial widget content
     */
    public function init()
    {
        if(is_bool($this->content)){
            $this->data = [
                'id'       => 0,
                'title'    => '',
                'position' => '',
                'content'  => ''
            ];
            $this->view = $this->widgetView;
        } else {
            $content = '';
            foreach ($this->content as $dataWidget) {
                if($this->listNames){
                    if(in_array($dataWidget['widget'], $this->listNames)){
                        $content .= $this->getContent($dataWidget);
                    }
                } else {
                    $content .= $this->getContent($dataWidget);
                }
            }
            $this->content = $content;
        }
        parent::init();
    }

    /*
     * render widget content
     */
    public function run()
    {
        return $this->render($this->view, ['data' => $this->data, 'content' => $this->content, 'flagView' => $this->widgetView]);
    }

    /*
     * get content by widget
     * @param string $dataWidget
     * @return string property $view
     */
    private function getContent($dataWidget){
        $class = 'app\components\helpers\packageWidget\models\\' . substr($dataWidget['widget'], 1);

        if(class_exists($class)){
            $contentPrepare = [
                'filePath'   => '/res/' . $this->identity . '/' . $this->identityId,
                'content' => $dataWidget['content']
            ];
            $dataView = [
                'id'         => $dataWidget['id'],
                'title'      => $dataWidget['title'],
                'identityId' => $this->identityId,
                'position'   => $dataWidget['position'],
                'content'    => $class::convertContent($contentPrepare),
                'params'     => ($this->widgetParams && isset($this->widgetParams[$dataWidget['widget']])) ? $this->widgetParams[$dataWidget['widget']] : []
            ];
            $view = $dataWidget['widget'] . '/' . $this->widgetView;
            return $this->render($view, ['data' => $dataView]);
        }
    }
}