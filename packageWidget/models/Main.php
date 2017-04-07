<?php
namespace app\components\helpers\packageWidget\models;

use app\components\helpers\CustomException;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * Модель по работе с выборками и вставками для данных контета виджетов.
 * Class Main
 * @package app\components\helpers\packageWidget\models
 *
 * @param int    $id
 * @param int    $widget_id
 * @param int    $position
 * @param int    $key
 * @param string $title
 * @param string $content
 *
 * @param string $tableName
 * @param string $identity
 */

class Main extends ActiveRecord {

    public static $tableName;
    private $identity;

    const BASE_DIR = 'res/';

    /*
     * @return string the table name
     */
    public static function tableName()
    {
        return '{{%' . self::$tableName . '}}';
    }

    /*
     * Set name to attribute identity
     * @param string $identity
     * @return mixed $this
     */
    public function initModel($identity){
        $this->identity = $identity;
        self::$tableName = $identity . '_content';
        return $this;
    }

    public function scenarios()
    {
        return [
            'save' => [$this->identity . '_id', 'widget_id', 'position', 'key', 'title', 'content']
        ];
    }

    /*
     * @param array $objContent
     * @param int $identityId
     * @param array &$uploadFiles
     * @throws Exception if there save model is error
     * @return bool the result to save content into table
     */
    public function saveContent(array $objContent, $identityId, &$uploadFiles){
        $arrId = [];

        try{
            foreach ($objContent as $wModel){
                $arrId[] = $wModel->id;
                $itemModel = self::findOne(['id' => $wModel->id]);
                $itemModel = ($itemModel) ? $itemModel : clone $this;
                $itemModel->scenario = 'save';

                $content = [
                    $this->identity . '_id' => $identityId,
                    'widget_id'             => $wModel->widgetId,
                    'position'              => $wModel->positionW,
                    'key'                   => '',
                    'title'                 => $wModel->title,
                    'content'               => $wModel->mapContent($uploadFiles)
                ];
                $itemModel->attributes = $content;
                $itemModel->save();
                $arrId[] = $itemModel->id;
            }

            if($arrId){
                $arrId = array_unique(array_filter($arrId));
                self::deleteAll('id NOT IN ('. implode(',', $arrId). ') AND '. $this->identity . '_id' .' = :identityId', [':identityId' => $identityId]);
            } else {
                self::deleteAll($this->identity . '_id' .' = :identityId', [':identityId' => $identityId]);
            }
            return true;
        }catch (\Exception $ex){
            throw new CustomException('Ошибка', $ex->getMessage(), $ex->getCode(), $ex);
            //return false;
        }
    }

    /*
     * @param int $identityId
     * @return array the  content by id
     */
    public function findContentById($identityId){
        $results = self::find()
                    ->select([self::tableName().'.*', 'wc.*'])
                    ->leftJoin('{{%widget_from_content}} wc', 'wc.widget_id = '. self::tableName() .'.widget_id')
                    ->where([''. $this->identity .'_id' => $identityId])
                    ->orderBy([self::tableName().'.position'=>SORT_ASC])
                    ->asArray()
                    ->all();
        return $results;
    }
}