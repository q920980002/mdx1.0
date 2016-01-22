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


    /**
     *用户实名认证接口
     */
    public function actionUserAuth(){

        $post = \Yii::$app->request->post();
        $chinapnrService = new ChinapnrService();

        $account_id = \Yii::$app->session['account_id'];
        $passport_id = \Yii::$app->session['passport_id'];
        $this->ajaxReturn($chinapnrService->authName($account_id,$passport_id,$post['name'],$post['idCardNo']));

    }

    /**
     * 绑定银行卡接口
     */
    public function actionBindBank(){

        $post = \Yii::$app->request->post();

        $chinapnrService = new ChinapnrService();
        $account_id = \Yii::$app->session['account_id'];
        $passport_id = \Yii::$app->session['passport_id'];
        $this->ajaxReturn($chinapnrService->bindBank($account_id,$post['BankCode'],$post['CardNo'],$post['phone'],$post['CityCode']));


    }




}