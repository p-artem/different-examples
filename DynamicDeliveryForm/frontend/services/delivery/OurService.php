<?php
namespace frontend\services\delivery;

use common\models\Order;
use yii\helpers\ArrayHelper;

/**
 * Class OurService
 * @package frontend\services\delivery
 */
class OurService implements DeliveryServiceInterface {

    public $cityKey;
    public $departmentKey;

    /**
     * @return array
     */
    public function getCities(){
        return [];
    }

    /**
     * @return array
     */
    public function getDepartments(){
        return [];
    }

    /**
     * @return mixed
     */
    public function findCity(){
        return '';
    }

    /**
     * @return string
     */
    public function findDepartment(){
        return '';
    }
}