<?php
namespace frontend\models\comment;

use Yii;
use common\models\comment\CommonComment;
use frontend\models\article\ArticleSingle;
use frontend\models\deception\DeceptionSingle;
use common\events\EventMongo;

/**
 * Class CommentSingle
 * @package frontend\models\comment
 *
 * @param string $url;
 * @param string $parent_url;
 * @param int    $parent_user_id;
 * @param string $parent_user_name;
 * @param string $parent_user_surname;
 * @param string $parent_user_avatar;
 * @param int    $parent_create_time;
 * @param string $parent_message;
 * @param int    $user_id;
 * @param string $user_name;
 * @param string $user_surname;
 * @param string $user_avatar;
 * @param string $message;
 * @param int    $create_time;
 * @param int    $update_time;
 */
class CommentSingle extends CommonComment
{
    public function scenarios()
    {
        return [
            'create' => ['url', 'parent_id', 'message', 'create_time', 'update_time'],
        ];
    }

    public function rules()
    {
        return [
            [['parent_id'], 'default', 'value' => 0],
            [
                ['parent_id'], 'filter',
                'filter' => function ($value) { return (int) $value; },
                'on' => ['create']
            ],
            [
                ['url', 'message'], 'required',
                'message' => 'Поле обязательно для заполенения',
                'on' => ['create']
            ],
            [
                ['url', 'message'], 'filter','filter' => 'trim',
                'on' => ['create']
            ],
            [
                ['message'], 'filter',
                'filter' => function ($value) { return trim(strip_tags(htmlspecialchars($value))); },
                'on' => ['create']
            ],
        ];
    }

    public function getComments($url, $page = 1, $one = false){
       
        $collection = Yii::$app->mongodb->getCollection(self::collectionName());
        if($count = $collection->count(['url' => $url])){
            $skip = ($page < 2) ? 0 : ($page - 1) * self::PAGE_LIMIT;

            if($one){
                $param1 = ['$sort' => ['comment_id' => -1]];
                $param2 = ['$limit' => 1];
            } else {
                 $param1 = ['$skip' => $skip];
                 $param2 = ['$limit' => self::PAGE_LIMIT];
            }

            $result = $collection->aggregate([
                ['$match' => ['url' => $url]],
                $param1, $param2,
                ['$lookup' => ['from' =>'user', 'localField' => "user_id", 'foreignField' => "user_id", 'as' => 'user']],
                ['$lookup' => ['from' => self::collectionName(), 'localField' => "parent_id", 'foreignField' => "comment_id", 'as' => 'parent']],
                ['$unwind' => '$user'],
                [
                    '$project' => [
                        '_id'         => 0,
                        'comment_id'  => 1,
                        'message'     => 1,
                        'create_time' => 1,
                        'user'        => 1,
                        'parent'      => ['$arrayElemAt' => ['$parent', 0]],

                    ]
                ],
                [
                    '$lookup' => ['from' =>'user', 'localField' => "parent.user_id", 'foreignField' => 'user_id', 'as' => 'parentInfo']
                ],
                [
                    '$project' => [
                        'comment_id'  => 1,
                        'message'     => 1,
                        'create_time' => 1,
                        'user' => 1,
                        'parent' => ['$ifNull' => ['$parent', []]],
                        'parentInfo' => ['$arrayElemAt' => ['$parentInfo', 0]],
                    ]
                ],
                [
                    '$project' => [
                        'comment_id' => 1, 'message' => 1, 'create_time' => 1, 'user' => 1,
                        'parent' => [
                            'message'     => '$parent.message',
                            'create_time' => '$parent.create_time',
                            'user_id'     => '$parentInfo.user_id',
                            'name'        => '$parentInfo.name',
                            'surname'     => '$parentInfo.surname',
                            'avatar'      => '$parentInfo.avatar',
                        ],
                    ]
                ]

            ]);

            foreach ($result as &$item){
                $item['user']['avatar'] = $this->mapAvatars($item['user']['avatar'], $item['user']['user_id']);
                $item['user']['fio'] = $item['user']['name'] . ' ' . $item['user']['surname'];
                if($item['parent']){
                    $item['parent']['avatar'] = $this->mapAvatars($item['parent']['avatar'], $item['parent']['user_id']);
                    $item['parent']['fio'] = $item['parent']['name'] . ' ' . $item['parent']['surname'];
                }
            }

            $totalPages = ceil($count/ self::PAGE_LIMIT);
            $nextPage = ($totalPages > 1 && $page <= $totalPages) ? $page + 1 : 1;

            return ['data' =>  $result, 'nextPage' => $nextPage, 'count' => $count, 'itemPage' => $page, 'totalPages' => (int) $totalPages];
        }
        return ['data' => [], 'nextPage' => 0, 'count' => 0, 'itemPage' => $page, 'totalPages' => 0];
    }

    public function create(){

        $url = explode('/', $this->url);
        if(count($url) !== 2) return false;
        $data = [];
        switch ($url[0]){
            case 'deception':
                $arr = explode('-', $url[1]);
                if(count($arr) != 2) return false;
                if(!DeceptionSingle::checkType($arr[0]) || !$data = DeceptionSingle::findForComment($arr[0], (int) $arr[1])) return false;
                break;
            case 'article':
                $articleId = (int) preg_replace('/[^0-9]/', '', $url[1]);
                if(!$data = ArticleSingle::findForComment((int) $articleId)) return false;
                break;
            default: return false;
        }

        $event = new EventMongo();
        $this->user_id      = Yii::$app->user->identity->getId();
        $this->user_name    = Yii::$app->user->identity->name;
        $this->user_surname = Yii::$app->user->identity->surname;
        $this->user_avatar  = Yii::$app->user->identity->avatar;


        $this->trigger(self::EVENT_CREATE_INDEX, $event);
        $collection = Yii::$app->mongodb->getCollection(self::collectionName());

        if($collection->insert($this->attributes)){
            $data['collection']->findAndModify(
                [$data['field'] => $data['id']],
                ['$inc' => ['count_comments' => 1]],
                ['new' => 1]
            );
            return true;
        }
        return false;
    }

    private function mapAvatars($avatar, $id){
        $hash = md5($id);
        $path = Yii::getAlias('@webroot') . '/res/users/' . $hash;
        if($avatar && file_exists($path . '/' . $avatar)){
            $image = Yii::getAlias('@hostName') . '/res/users/' . $hash . '/' . $avatar;
        } else {
            $image = Yii::getAlias('@hostName') . '/images/default_user_avatar.png';
        }
        return $image;
    }

}