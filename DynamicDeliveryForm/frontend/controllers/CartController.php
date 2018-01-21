<?php

namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\ProductCartPosition;
use common\models\User;
use common\models\Product;
use frontend\models\OrderFastForm;
use frontend\models\OrderForm;
use frontend\models\PromoForm;
use frontend\modules\user\models\LoginForm;
use frontend\modules\user\models\SignupFastForm;
use frontend\modules\user\models\SignupForm;
use frontend\models\DeliveryForm;
use frontend\managers\DeliveryManager;
use yii\base\Model;
/**
 * Class CartController
 * @package frontend\controllers
 */
class CartController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'add' => ['ajax get'],
                ],
            ],
        ];
    }


    /**
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDeliveryMethod($id){

        $model = new DeliveryForm();
        $model->delivery_id = $id;

        if(!Yii::$app->request->isAjax || !$view = $model->getDeliveryView()){
            throw new NotFoundHttpException(Yii::t('site', 'Page not found'));
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'desktop' =>
                $this->renderAjax('delivery/'. $view, [
                    'model'     => $model,
                    'forMobile' => false]),
            'mobile' => $this->renderAjax('delivery/'. $view, [
                'model'     => $model,
                'forMobile' => true]),

        ];
    }

    /**
     * @param $id
     * @param $token
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionCityDepartments($id, $token){
        if(!Yii::$app->request->isAjax){
            throw new NotFoundHttpException(Yii::t('site', 'Page not found'));
        }
        $service = (new DeliveryManager)->getService($id);
        $service->cityKey = $token;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $service->getDepartments();
    }
}