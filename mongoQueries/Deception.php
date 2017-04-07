<?php
namespace frontend\models\deception;

use Yii;
use common\events\EventMongo;
use common\models\deception\CommonDeception;
use yii\mongodb\Query;
/**
 * Class DeceptionSingle
 * @package frontend\models\deception
 */
class DeceptionSingle extends CommonDeception
{
    public static function findById($type, $identityId){
        $collection = Yii::$app->mongodb->getCollection(self::collectionName($type));
        $favCollection = Yii::$app->mongodb->getCollection('favorite');
        $newData = [];
        $data = $collection->aggregate([
            ['$match' => ['identity_id' => (int) $identityId, 'status' => self::STATUS_ACTIVE]],
            ['$project' => [
                    'data' => '$$ROOT',
                    'history' => 1,
                    'microData' => [
                        'name'  => ['$ifNull' => [['$arrayElemAt' => ['$name_identity', 0]], '']],
                        'email' => ['$ifNull' => [['$arrayElemAt' => ['$email', 0]], '']],
                        'phone' => ['$ifNull' => [['$arrayElemAt' => ['$phone', 0]], '']],
                        'locate' => ['$arrayElemAt' => ['$addresses', 0]],
                        'position' => [
                            '$arrayElemAt' => [
                                [
                                    '$map' => [
                                        'input' => '$history',
                                        'as'    => 'item',
                                        'in'    => '$$item.work_position',
                                    ]
                                ],
                                0]
                        ]
                    ]
                ]
            ],
            ['$unwind' => '$history'],
            [
                '$lookup' => [
                    'from' => 'user',
                    'localField' => "history.user_id",
                    'foreignField' => "user_id",
                    'as' => 'item'
                ]
            ],
            ['$unwind' => '$item'],
            [
                '$group' => [
                    '_id' => '$_id',
                    'info' => ['$push' =>'$data'],
                    'microData' => ['$push' =>'$microData'],
                    'users'  => ['$addToSet' => '$item'],
                ]
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'info' => ['$arrayElemAt' => ['$info', 0]],
                    'users' => 1,
                    'microData' => ['$arrayElemAt' => ['$microData', 0]],
                ]
            ]
        ]);

        if($data){
            $data = array_shift($data);
            $newData = array_merge($data['info'], ['users' => $data['users']]);
            $isFav = false;
            if(!Yii::$app->user->isGuest){
                $isFav = (bool) $favCollection->findOne(['type' => $type, 'identity_id' => $identityId, 'users' => (int) Yii::$app->user->identity->getId()]);
            }
            $newData['key_table'] = $type;
            $newData['isFavorite'] = $isFav;
            $newData['history'] = self::mapHistoryImages($identityId, $newData['history']);

            if($data['microData']){
                $mData = $data['microData'];
                if(isset($mData['locate']) && $mData['locate']){
                    $cityId = (int) $mData['locate']['city_id'];
                    $phoneCode = (int) Yii::$app->db->createCommand(" SELECT phone_code FROM {{%city}} WHERE id=" . $cityId)->queryScalar();
                    $mData['locate']['code'] = $phoneCode;
                } else {
                    $mData['locate'] = ['code' => '', 'address' => '', 'city_name' => ''];
                }
                $newData['microData'] = $mData;
            } else {
                $newData['microData'] = [];
            }
        }
        return $newData;
    }

}