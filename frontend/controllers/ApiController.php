<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/1/19
 * Time: 下午3:10
 */
namespace frontend\controllers;
use frontend\service\ChinapnrService;
use yii\web\Controller;

class ApiController extends Controller{


    public function actionUserAuth(){

        $post = \Yii::$app->request->post();
        $chinapnrService = new ChinapnrService();

        $account_id = \Yii::$app->session['account_id'];
        $this->ajaxReturn($chinapnrService->authName($account_id,$post['name'],$post['idCardNo']));

    }




}