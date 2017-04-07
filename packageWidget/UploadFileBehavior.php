<?php
namespace app\components\helpers\packageWidget;

use yii\base\Behavior;
use yii\base\Model;

class UploadFileBehavior extends Behavior {

    public $fromAttribute;
    public $toAttribute;

    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => 'uploadFiles',
        ];
    }

    public function uploadFiles($event){
        $this->owner->{$this->toAttribute} = $this->fromAttribute;
    }
}