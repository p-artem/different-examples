<?php

namespace frontend\services\delivery;

use Yii;
use LisDev\Delivery\NovaPoshtaApi2;
use yii\helpers\ArrayHelper;
use yii\caching\TagDependency;

/**
 * Class NovaPoshtaService
 * @package frontend\services\delivery
 */
class NovaPoshtaService implements DeliveryServiceInterface {

    public $cityKey;
    public $departmentKey;

    /**
     * @return array|mixed
     */
    public function getCities(){

        $cache = Yii::$app->cache;
        $cities = $cache->get('novaPoshtaCities');

        if(!$cities){
            $np = new NovaPoshtaApi2(
                Yii::$app->keyStorage->get('nova_poshta_api_key'),
                'ru', // Язык возвращаемых данных: ru (default) | ua | en
                FALSE, // При ошибке в запросе выбрасывать Exception: FALSE (default) | TRUE
                'curl' // Используемый механизм запроса: curl (defalut) | file_get_content
            );

            $result = ArrayHelper::getValue($np->getCities(), 'data');
            if($result) {
                $cities = ArrayHelper::map($result, 'Ref', 'DescriptionRu');

                $cache->set('novaPoshtaCities', $cities, 86400,
                    new TagDependency(['tags' => ['cities']]));
            } else {
                $cities = [];
            }
        }
        return $cities;
    }

    /**
     * @return array
     */
    public function getDepartments(){

        $np = new NovaPoshtaApi2(
            Yii::$app->keyStorage->get('api_key_nova_poshta'),
            'ru',
            FALSE,
            'curl'
        );

        $departments = [];
        $result = ArrayHelper::getValue($np->getWarehouses($this->cityKey), 'data');
        if($result){
            $departments = ArrayHelper::map($result, 'Ref','DescriptionRu');
        }
        return $departments;
    }

    /**
     * @return mixed
     */
    public function findCity(){
        return ArrayHelper::getValue($this->getCities(), $this->cityKey);
    }

    /**
     * @return mixed
     */
    public function findDepartment(){
        return ArrayHelper::getValue($this->getDepartments(), $this->departmentKey);
    }
}